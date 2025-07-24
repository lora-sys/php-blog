# GEMINI.md - 技术档案：PHP & SQLite 博客项目

本文档由 Gemini 自动生成，旨在分析和归档当前项目的技术状态、架构和关键实现。最后更新于 2025年7月25日。

## 1. 项目概述 📝

这是一个使用原生PHP和SQLite数据库构建的轻量级博客系统。项目采用纯过程化编程风格，没有使用任何外部框架，使其成为一个非常适合学习PHP基础和Web开发核心概念的范例。

**核心功能：**
- **前台展示**：文章列表、文章详情页、评论查看与发表。
- **后台管理**：管理员登录/登出、文章列表管理（编辑/删除）、文章创建与更新。

## 2. 技术栈与架构 🛠️

- **后端语言**: PHP (原生)
- **数据库**: SQLite (通过PDO扩展进行交互)
- **前端**: HTML, CSS (原生)
- **架构风格**: 过程化编程 (Procedural Programming)

### 目录结构分析

- `/`: 项目根目录，包含面向用户的主文件（`index.php`, `view-post.php` 等）。
- `/assets`: 存放静态资源，主要是 `main.css` 样式文件。
- `/data`: 存放数据文件，包括 `data.sqlite` 数据库文件和 `init.sql` 初始化脚本。
- `/lib`: 存放核心业务逻辑的库文件，实现了良好的功能分离。
  - `common.php`: 存放最通用的函数，如数据库连接 (`getPDO`)、安全转义 (`htmlEscape`)、用户会话 (`isLoggedIn`) 等。
  - `edit-post.php`: 存放与创建和编辑文章相关的函数 (`addPost`, `editPost`)。
  - `list-posts.php`: 存放与后台文章列表相关的函数 (`getAllPosts`, `deletePost`)。
  - `view-post.php`: 存放与文章/评论查看相关的函数 (`getPostRow`, `writeCommentForm`)。
  - `install.php`: 存放安装逻辑 (`installBlog`, `createUser`)。
- `/templates`: 存放可重用的HTML片段，如页眉 (`head.php`)、页头 (`title.php`)、评论表单等。

## 3. 关键实现分析 🔍

### 数据库设计与交互
- 使用 **SQLite** 作为文件型数据库，配置简单，无需独立的服务进程。
- 通过 **PDO (PHP Data Objects)** 扩展与数据库通信，这是现代PHP推荐的标准做法，能有效防止SQL注入（当使用预处理语句时）。
- **数据库初始化 (`init.sql`)**: 脚本负责创建 `user`, `post`, `comment` 三张核心表，并定义了它们之间的**外键约束 (FOREIGN KEY)**，确保了数据的引用完整性（例如，评论必须属于一篇文章，文章必须属于一个用户）。
- **数据库事务 (Transaction)**: 在 `deletePost` 函数中正确地使用了事务 (`beginTransaction`, `commit`, `rollBack`)。这是一个亮点，它保证了在删除文章及其所有评论时，操作是原子性的，要么都成功，要么都失败，有效防止了数据不一致。

### 安全性
- **输出转义**: 所有输出到HTML的动态数据都使用了 `htmlEscape` 函数进行处理，有效防止了 **XSS (跨站脚本) 攻击**。
- **访问控制**: 通过 `isLoggedIn()` 会话函数对后台页面 (`edit-post.php`, `list-posts.php`) 进行了访问控制，只有登录用户才能访问。
- **.htaccess 配置**: 通过 `.htaccess` 文件，禁止了对 `data`, `lib`, `templates` 等核心目录的直接URL访问，返回404错误，这是一个重要的安全加固措施。

### 后台功能逻辑
- **新建与编辑复用**: `edit-post.php` 页面通过检查URL中是否存在 `post_id`，实现了“新建”和“编辑”功能的复用，这是Web开发中的常见模式。
- **表单驱动的删除**: `list-posts.php` 页面中的删除功能是通过一个大表单 (`<form>`) 实现的。每个删除按钮都是一个 `submit` 类型的 `input`，通过其 `name` 属性 (`delete-post[id]`) 来区分要删除哪篇文章。这种方法虽然可行，但在现代Web开发中，更常见的做法是为每个删除操作创建一个独立的链接或表单。

## 4. 潜在的改进方向 ✨

- **RESTful API**: 可以将后端的数据库操作（增删改查）封装成一套RESTful API，这样前端就可以通过JavaScript（例如使用 `fetch` API）与之交互，实现更流畅的异步操作（如无刷新删除、AJAX提交评论等），并为未来的前后端分离打下基础。
- **前端路由/单页应用 (SPA)**: 引入一个轻量级的前端路由库（如 Navigo）或框架（如 Vue.js, React），可以将项目改造成一个单页应用，提升用户体验。
- **对象关系映射 (ORM)**: 引入一个ORM库（如 Eloquent 或 Doctrine），可以用面向对象的方式来操作数据库，替代手写SQL，使代码更优雅、更易于维护。
- **依赖管理**: 引入 **Composer** 作为PHP的依赖管理器，可以方便地集成和管理第三方库（如ORM、路由库等）。
- **模板引擎**: 使用 **Twig** 或 **Blade** 这样的模板引擎，可以使 `/templates` 目录下的视图文件更简洁、更安全，实现视图与逻辑的更彻底分离。
