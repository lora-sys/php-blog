/* 开启外键约束 */
PRAGMA foreign_keys = ON;

/* 用户表 */
DROP TABLE IF EXISTS user;
CREATE TABLE user (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    username VARCHAR NOT NULL,
    password VARCHAR NOT NULL,
    created_at VARCHAR NOT NULL,
    is_enabled BOOLEAN NOT NULL DEFAULT true
);

/* 插入初始管理员用户，密码将在安装脚本中被哈希替换 */
INSERT INTO
    user (username, password, created_at, is_enabled)
VALUES
    ("admin", "unhashed-password", datetime('now', '-3 months'), 1);

/* 文章表 */
DROP TABLE IF EXISTS post;
CREATE TABLE post (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    title VARCHAR NOT NULL,
    body VARCHAR NOT NULL,
    user_id INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    updated_at VARCHAR,
    FOREIGN KEY (user_id) REFERENCES user(id)
);

/* 评论表 */
DROP TABLE IF EXISTS comment;
CREATE TABLE comment (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    post_id INTEGER NOT NULL,
    created_at VARCHAR NOT NULL,
    name VARCHAR NOT NULL,
    website VARCHAR,
    text VARCHAR NOT NULL,
    FOREIGN KEY (post_id) REFERENCES post(id)
);

/* 插入一些初始的文章和评论数据 */
INSERT INTO
    post (title, body, user_id, created_at)
VALUES
    ("Here's our first post", "This is the body of the first post.\nIt is split into paragraphs.", 1, date('now', '-2 months'));

INSERT INTO
    post (title, body, user_id, created_at)
VALUES
    ("Now for a second article", "This is the body of the second post.\nThis is another paragraph.", 1, date('now', '-40 days'));

INSERT INTO
    post (title, body, user_id, created_at)
VALUES
    ("Here's a third post", "This is the body of the third post.\nThis is split into paragraphs.", 1, date('now', '-13 days'));

INSERT INTO
    comment (post_id, created_at, name, website, text)
VALUES
    (1, date('now', '-10 days'), 'Jimmy', 'http://example.com', 'This is a comment on the first post.');

INSERT INTO
    comment (post_id, created_at, name, website, text)
VALUES
    (2, date('now', '-8 days'), 'Jonny', 'http://example1.com', 'This is a comment on the second post.');