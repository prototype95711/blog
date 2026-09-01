# Project

Simply blog by native PHP with Smarty template engine

Author: Gleb Perfiliev

# Capabilities:

  - Blog pages (categories, posts, and multiple nesting)
    - Posts sorting and pagination
    - Views counter
    - Related posts block
  - Home page with categories and recent blog posts
  - Seeding posts and categories. 

# Seeding usage

```bash
docker exec Blog php bin/seeding.php
  [--categories=N] [--posts=N] [--clear] 
```

Where:
 - `categories` is number of categories, default=10
 - `posts` is number of posts, default=20
 - `clear` clears all categories/posts/links/images before seeding

# AI was used for

- For fast managing the environment
- Preparation of a draft templates
- Bug hunting
