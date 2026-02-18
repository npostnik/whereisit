(function(){
  function updateRowClass(checkbox){
    var tr = checkbox.closest('tr');
    if(!tr) return;
    if(checkbox.checked){
      tr.classList.add('checked');
    } else {
      tr.classList.remove('checked');
    }
  }

  function init(){
    var table = document.querySelector('.whereisit table, .whereisit-table');
    if(!table) return;
    var checkboxes = table.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function(cb){
      // initialize state on load
      updateRowClass(cb);
      cb.addEventListener('change', function(){ updateRowClass(cb); });
    });
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
