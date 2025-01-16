document.addEventListener('DOMContentLoaded', () => {
  // Select all table rows inside the <tbody>
  const rows = document.querySelectorAll('tbody tr');

  rows.forEach((row, index) => {
    // Add a delay to each row's animation based on its position
    row.style.animationDelay = `${index * 0.01}s`;
    // Add the animation class
    row.classList.add('slide-in');
  });
});

document.getElementById('search-button').addEventListener('click', async () => {
  const searchInput = document.getElementById('search-input').value;

  try {
    // Fetch search results asynchronously
    const response = await fetch(`/search?author=${encodeURIComponent(searchInput)}`);

    if (!response.ok) {
        throw new Error(`Error: ${response.status}`);
    }

    const results = await response.json();

    // Render results dynamically
    renderResults(results);
  } catch (error) {
      console.error('Search failed:', error);
      document.getElementById('results-container').innerHTML = `<p>Error fetching results. Please try again later.</p>`;
  }
});

// Function to render results in the DOM
function renderResults(results) {
  const container = document.querySelector('#results-container table tbody');
  container.innerHTML = ''; // Clear previous results

  if (results.length === 0) {
    const tableRow = document.createElement('tr');
    tableRow.innerHTML = `
      <td colspan="2">
        <p class="no-results">No results found.</p>
      </td>
    `;
    tableRow.classList.add('slide-in');

    container.appendChild(tableRow);

    return;
  }

  results.forEach((row, index) => {
    const bookName = row.book_name || '<none> (no books found)';
    
    const tableRow = document.createElement('tr');
    tableRow.innerHTML = `
      <td>${escapeHTML(row.author_name)}</td>
      <td>${escapeHTML(bookName)}</td>
    `;
    tableRow.style.animationDelay = `${index * 0.03}s`;
    tableRow.classList.add('slide-in');

    container.appendChild(tableRow);
  });
}

// Helper function to escape HTML
function escapeHTML(str) {
  const span = document.createElement('span');
  span.textContent = str;
  return span.innerHTML;
}


