<?php ob_start(); ?>

<h1>Search Authors and Books</h1>

<div id="search-container">
    <input type="text" id="search-input" placeholder="Search authors or books..." />
    <button id="search-button">Search</button>
</div>

<div id="results-container">
    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Book</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['author_name']) ?></td>
                        <td><?= htmlspecialchars($row['book_name'] ?: '<none> (no books found)') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">No authors or books found</p>
    <?php endif; ?>
</div>

<?php 
    $content = ob_get_clean(); 
    require_once __DIR__ . '/layouts/main.php';
?>
