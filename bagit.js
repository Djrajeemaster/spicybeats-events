const dealsContainer = document.getElementById('deals-container');
const categorySelect = document.getElementById('category-select');

// Load categories into dropdown
fetch('api/get_categories.php')
  .then(res => res.json())
  .then(categories => {
    categories.forEach(cat => {
      const option = document.createElement('option');
      option.value = cat.name;
      option.textContent = cat.name;
      categorySelect.appendChild(option);
    });
  });

categorySelect.addEventListener('change', () => {
  loadDeals(categorySelect.value);
});

function vote(dealId, type) {
  fetch('api/vote.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ deal_id: dealId, vote: type }) // 👈 matches PHP
  })
  .then(res => res.json())
  .then(data => {
    if (data.success && typeof data.total_votes === 'number') {
      const voteElement = document.getElementById(`vote-count-${dealId}`);
      if (voteElement) {
        voteElement.textContent = `Votes: ${data.total_votes}`;
      }
    } else {
      alert(data.error || 'Vote failed');
    }
  })
  .catch(err => {
    console.error('Vote error:', err);
    alert('Error submitting vote');
  });
}

function loadDeals(category = '') {
  let url = 'api/get_deals.php';
  if (category) {
    url += `?category=${encodeURIComponent(category)}`;
  }

  fetch(url)
    .then(response => response.json())
    .then(deals => {
      dealsContainer.innerHTML = '';
      deals.forEach(deal => {
        const card = document.createElement('div');
        card.className = 'deal-card';
        card.innerHTML = `
          ${deal.image ? `<img src="images/${deal.image}" alt="${deal.title}" style="width:100%; border-radius:8px;" />` : ''}
          <h2>${deal.title}</h2>
          <p>${deal.description}</p>
          <span class="badge">${deal.status}</span>
          <div style="margin-top: 10px;">
            <button onclick="vote(${deal.id}, 'up')">👍</button>
            <button onclick="vote(${deal.id}, 'down')">👎</button>
            <span id="vote-count-${deal.id}">Votes: 0</span>
          </div>
          <a class="view-button" href="deal.html?id=${deal.id}">View Deal</a>
        `;
        dealsContainer.appendChild(card);
      });
    })
    .catch(error => {
      console.error('Error loading deals:', error);
      dealsContainer.innerHTML = '<p>Failed to load deals. Please try again later.</p>';
    });
}

loadDeals();
