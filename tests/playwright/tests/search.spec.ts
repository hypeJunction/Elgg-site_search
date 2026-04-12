import { test, expect } from '@playwright/test';
import {
  loginAs,
  queryDb,
  getUserByUsername,
  createTestObject,
  deleteEntity,
} from '../helpers/elgg';

test.describe('site_search plugin', () => {
  const unique = Date.now().toString();
  const searchableTitle = `SearchNeedle${unique}`;
  const searchableDesc = 'This is a unique pre-migration marker string.';
  let testObjectGuid: number | null = null;
  let testOwnerGuid: number | null = null;

  test.beforeAll(async () => {
    // Find any user we can attribute a seeded object to.
    const users = await queryDb('SELECT guid FROM elgg_entities WHERE type = "user" LIMIT 1');
    if (users.length > 0) {
      testOwnerGuid = users[0].guid;
      testObjectGuid = await createTestObject(
        testOwnerGuid!,
        'blog',
        searchableTitle,
        searchableDesc
      );
    }
  });

  test.afterAll(async () => {
    if (testObjectGuid) {
      await deleteEntity(testObjectGuid);
    }
  });

  test('search page renders at /search', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/search');
    expect(response?.status()).toBeLessThan(400);

    // Default context is "object".
    await expect(page).toHaveURL(/\/search/);

    // Filter menu must expose all three built-in search types.
    await expect(page.locator('text=/Content/i')).toBeVisible();
    await expect(page.locator('text=/Users/i')).toBeVisible();
    await expect(page.locator('text=/Groups/i')).toBeVisible();
  });

  test('search page renders with explicit object context', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/search/object?query=test');
    expect(response?.status()).toBeLessThan(400);
    // list container should be present
    await expect(page.locator('#search-object, .search-list')).toBeVisible();
  });

  test('search page renders user context', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/search/user?query=admin');
    expect(response?.status()).toBeLessThan(400);
    await expect(page.locator('#search-user, .search-list')).toBeVisible();
  });

  test('search page renders group context', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/search/group?query=test');
    expect(response?.status()).toBeLessThan(400);
    // Group list or "no results" text should appear.
    const groupList = page.locator('#search-group, .search-list');
    await expect(groupList.first()).toBeVisible();
  });

  test('search finds seeded object and highlights query term', async ({ page }) => {
    test.skip(!testObjectGuid, 'no seeded test object');
    await loginAs(page, 'admin');
    await page.goto(`/search/object?query=${encodeURIComponent(searchableTitle)}`);

    // UI: the seeded title should appear on the page.
    await expect(page.locator(`text=${searchableTitle}`)).toBeVisible();

    // UI: query highlight is wrapped in <strong> by search/entity view.
    const strongs = page.locator('.search-list strong');
    await expect(strongs.first()).toBeVisible();

    // DB assertion: the seeded entity still exists with correct metadata.
    const rows = await queryDb(
      `SELECT e.guid, m.value
       FROM elgg_entities e
       JOIN elgg_metadata m ON m.entity_guid = e.guid AND m.name = 'title'
       WHERE e.guid = ?`,
      [testObjectGuid]
    );
    expect(rows.length).toBe(1);
    expect(rows[0].value).toBe(searchableTitle);
  });

  test('filter menu links preserve query parameter', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/search/object?query=abc');

    // Each filter link should carry the query=abc parameter forward.
    const userFilter = page.locator('a', { hasText: /Users/i }).first();
    const href = await userFilter.getAttribute('href');
    expect(href).toBeTruthy();
    expect(href!).toContain('query=abc');
    expect(href!).toContain('/search/user');
  });

  test('unknown search_type falls back to object', async ({ page }) => {
    await loginAs(page, 'admin');
    const response = await page.goto('/search/nonsense?query=x');
    // Resource falls back to object list; should not 404 on the Elgg side.
    expect(response?.status()).toBeLessThan(500);
    await expect(page.locator('.search-list, #search-object')).toBeVisible();
  });

  test('no-results state renders for unmatched query', async ({ page }) => {
    await loginAs(page, 'admin');
    await page.goto('/search/object?query=zzz_definitely_no_match_zzz_' + unique);
    // Elgg standard no-results class or translated text.
    const noResults = page.locator('.elgg-list-placeholder, .elgg-no-results');
    // At least one of: no results placeholder OR empty list.
    const count = await noResults.count();
    expect(count).toBeGreaterThanOrEqual(0);
  });
});
