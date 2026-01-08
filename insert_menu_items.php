<?php
require 'config.php';

if ($db_error) {
    die("❌ Database connection error: $db_error");
}

// Sample Tanzanian menu items
$menu_items = [
    // Starters
    [
        'name' => 'Samosa',
        'price' => 5000.00,
        'category' => 'Starters',
        'description' => 'Crispy fried pastry filled with spiced potatoes, peas, and lentils.',
        'image_url' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    [
        'name' => 'Chapati',
        'price' => 3000.00,
        'category' => 'Starters',
        'description' => 'Soft and fluffy flatbread, perfect as an appetizer.',
        'image_url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    // Main Course
    [
        'name' => 'Ugali with Nyama Choma',
        'price' => 15000.00,
        'category' => 'Main Course',
        'description' => 'Steamed maize meal served with grilled beef, a Tanzanian staple.',
        'image_url' => 'https://images.unsplash.com/photo-1541599468348-e96984315621?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    [
        'name' => 'Pilau',
        'price' => 12000.00,
        'category' => 'Main Course',
        'description' => 'Fragrant spiced rice dish with meat, onions, and aromatic spices.',
        'image_url' => 'https://images.unsplash.com/photo-1565299507177-b0ac66763828?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    // Desserts
    [
        'name' => 'Mandazi',
        'price' => 2000.00,
        'category' => 'Desserts',
        'description' => 'Sweet fried dough, similar to doughnuts, dusted with sugar.',
        'image_url' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    [
        'name' => 'Halva',
        'price' => 4000.00,
        'category' => 'Desserts',
        'description' => 'Sweet confection made from sunflower seeds and sugar.',
        'image_url' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    // Beverages
    [
        'name' => 'Chai',
        'price' => 2500.00,
        'category' => 'Beverages',
        'description' => 'Traditional spiced tea with milk and aromatic spices.',
        'image_url' => 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ],
    [
        'name' => 'Soda',
        'price' => 3000.00,
        'category' => 'Beverages',
        'description' => 'Refreshing carbonated soft drink.',
        'image_url' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'
    ]
];

echo "<h2>Inserting Sample Menu Items</h2>";
echo "<pre>";

foreach ($menu_items as $item) {
    $name = $conn->real_escape_string($item['name']);
    $price = $item['price'];
    $category = $conn->real_escape_string($item['category']);
    $description = $conn->real_escape_string($item['description']);
    $image_url = $conn->real_escape_string($item['image_url']);

    $sql = "INSERT INTO menu (name, price, category, description, image_url) VALUES ('$name', $price, '$category', '$description', '$image_url')";

    if ($conn->query($sql)) {
        echo "✅ Inserted: {$item['name']}\n";
    } else {
        echo "❌ Error inserting {$item['name']}: " . $conn->error . "\n";
    }
}

echo "\n✅ All sample items inserted successfully!\n";

$conn->close();
?>