  function openEditModal(product, flavor, stock) {
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('modal-product').value = product;
    document.getElementById('modal-flavor').value = flavor;
    document.getElementById('modal-stock').value = stock;
    document.getElementById('modal-product-name').textContent = product;
    document.getElementById('modal-flavor-name').textContent = flavor;
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  document.getElementById('editStockForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const product = document.getElementById('modal-product').value;
    const flavor = document.getElementById('modal-flavor').value;
    const newStock = document.getElementById('modal-stock').value;

    alert(`Updated ${product} (${flavor}) to ${newStock} units.`); 
    // Replace this with actual AJAX call to update in DB

    closeEditModal();
  });

  // Optional: close modal on outside click
  window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
      closeEditModal();
    }
  }
