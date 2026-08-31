<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Database;
use Faker\Factory;

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$options = getopt('', ['categories:', 'posts:', 'clear']);
$categoriesCount = isset($options['categories']) ? max(0, (int) $options['categories']) : 10;
$postsCount = isset($options['posts']) ? max(0, (int) $options['posts']) : 20;
$clear = array_key_exists('clear', $options);

$appRoot = dirname(__DIR__);
$imagesDir = $appRoot . '/public/images';

if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

$pdo = Database::connection();
$faker = Factory::create();

if ($clear) {
    echo "Clearing categories, posts, links and seeded images...\n";
    
    $pdo->exec('DELETE FROM categories_links');
    $pdo->exec('DELETE FROM posts');
    $pdo->exec('DELETE FROM categories');
    $pdo->exec('DELETE FROM images');

    foreach (glob($imagesDir . '/seed-*.png') as $file) 
    {
        unlink($file);
    }
}

echo "Seeding categories...\n";

$cat = $pdo->prepare('INSERT INTO categories (title, descr, parent_id) VALUES (:title, :descr, :parent)');
$categoryIds = [];
$nestFactor = 0.3;
$parentId = 0;
$max = mt_getrandmax();

for ($i = 0; $i < $categoriesCount; $i++) {
    $cat->execute([
        'title' => ucfirst($faker->words(2, true)),
        'descr' => $faker->sentence(12),
        'parent' => $parentId
    ]);
    $categoryId = (int) $pdo->lastInsertId();

    if ($categoryId > 0) {
        $categoryIds[$categoryId] = $categoryId;
        $parentId = mt_rand() / $max < $nestFactor
            ? $categoryId
            : 0;
    }
}

if (empty($categoryIds)) {
    $categoryIds = array_column($pdo->query('SELECT id FROM categories')->fetchAll(), 'id');
}

echo "Seeding posts...\n";

$insertImage = $pdo->prepare('INSERT INTO images (filepath) VALUES (:filepath)');
$insertPost = $pdo->prepare(
    'INSERT INTO posts (title, descr, content, image_id, views, created_at)'
    . ' VALUES (:title, :descr, :content, :image_id, :views, :created_at)'
);
$insertLink = $pdo->prepare('INSERT INTO categories_links (category_id, post_id) VALUES (:category_id, :post_id)');

for ($i = 0; $i < $postsCount; $i++) {
    $title = ucfirst($faker->sentence(6));

    $filename = 'seed-' . bin2hex(random_bytes(6)) . '.png';
    generatePostImage($imagesDir . '/' . $filename, $title);

    $insertImage->execute(['filepath' => '/images/' . $filename]);
    $imageId = (int) $pdo->lastInsertId();

    $insertPost->execute([
        'title' => $title,
        'descr' => $faker->paragraphs(1, true),
        'content' => $faker->paragraphs(3, true),
        'image_id' => $imageId,
        'views' => $faker->numberBetween(0, 500),
        'created_at' => $faker->dateTimeBetween('-1 year')->format('Y-m-d H:i:s'),
    ]);
    $postId = (int) $pdo->lastInsertId();

    $insertLink->execute([
        'category_id' => empty($categoryIds) ? 0 : $categoryIds[array_rand($categoryIds)],
        'post_id' => $postId
    ]);
}

echo "Done.\n";

function generatePostImage(string $path, string $label): void
{
    $width = 800;
    $height = 600;

    $image = imagecreatetruecolor($width, $height);
    $background = imagecolorallocate($image, random_int(60, 200), random_int(60, 200), random_int(60, 200));
    imagefill($image, 0, 0, $background);

    $textColor = imagecolorallocate($image, 255, 255, 255);
    $font = 5;
    $lineHeight = imagefontheight($font) + 4;
    $lines = explode("\n", wordwrap($label, 46, "\n", true));

    $y = (int) (($height - count($lines) * $lineHeight) / 2);

    foreach ($lines as $line) {
        $x = (int) (($width - imagefontwidth($font) * strlen($line)) / 2);
        imagestring($image, $font, max(0, $x), $y, $line, $textColor);
        $y += $lineHeight;
    }

    imagepng($image, $path);
    imagedestroy($image);
}
