import { Page } from '@playwright/test';
import mysql from 'mysql2/promise';

const DB_CONFIG = {
  host: process.env.ELGG_DB_HOST || 'db',
  port: Number(process.env.ELGG_DB_PORT || 3306),
  user: process.env.ELGG_DB_USER || 'elgg',
  password: process.env.ELGG_DB_PASS || 'elgg',
  database: process.env.ELGG_DB_NAME || 'elgg',
};

export async function loginAs(page: Page, username: string, password: string = 'admin12345') {
  await page.goto('/login');
  await page.fill('.elgg-module-aside input[name="username"]', username);
  await page.fill('.elgg-module-aside input[name="password"]', password);
  await page.click('.elgg-module-aside button[type="submit"]');
  await page.waitForURL(url => !url.toString().includes('/login'), { timeout: 15000 });
}

export async function queryDb(sql: string, params: any[] = []): Promise<any[]> {
  const conn = await mysql.createConnection(DB_CONFIG);
  const [rows] = await conn.execute(sql, params);
  await conn.end();
  return rows as any[];
}

export async function getEntity(guid: number) {
  return queryDb('SELECT * FROM elgg_entities WHERE guid = ?', [guid]);
}

export async function getEntitiesBySubtype(subtype: string) {
  return queryDb('SELECT * FROM elgg_entities WHERE subtype = ?', [subtype]);
}

export async function getUserByUsername(username: string) {
  const rows = await queryDb(
    `SELECT e.* FROM elgg_entities e
     JOIN elgg_users_entity u ON u.guid = e.guid
     WHERE u.username = ?`,
    [username]
  );
  return rows[0];
}

export async function createTestObject(ownerGuid: number, subtype: string, title: string, description: string) {
  const conn = await mysql.createConnection(DB_CONFIG);
  try {
    const [result]: any = await conn.execute(
      `INSERT INTO elgg_entities (type, subtype, owner_guid, container_guid, access_id, time_created, time_updated, enabled)
       VALUES ('object', ?, ?, ?, 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'yes')`,
      [subtype, ownerGuid, ownerGuid]
    );
    const guid = result.insertId;
    await conn.execute(
      `INSERT INTO elgg_metadata (entity_guid, name, value, value_type, time_created)
       VALUES (?, 'title', ?, 'text', UNIX_TIMESTAMP()), (?, 'description', ?, 'text', UNIX_TIMESTAMP())`,
      [guid, title, guid, description]
    );
    return guid as number;
  } finally {
    await conn.end();
  }
}

export async function deleteEntity(guid: number) {
  const conn = await mysql.createConnection(DB_CONFIG);
  try {
    await conn.execute('DELETE FROM elgg_metadata WHERE entity_guid = ?', [guid]);
    await conn.execute('DELETE FROM elgg_entities WHERE guid = ?', [guid]);
  } finally {
    await conn.end();
  }
}
