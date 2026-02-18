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

    function initCheckboxes(){
        var table = document.querySelector('.whereisit table, .whereisit-table');
        if(!table) return;
        var checkboxes = table.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function(cb){
            updateRowClass(cb);
            cb.addEventListener('change', function(){ updateRowClass(cb); });
        });
    }

    function initFilterSelect(){
        var container = document.querySelector('[data-whereisit-select]');
        if(!container) return;

        var search = container.querySelector('[data-whereisit-search]');
        var dropdown = container.querySelector('[data-whereisit-dropdown]');
        var hidden = container.querySelector('[data-whereisit-hidden]');
        var options = dropdown.querySelectorAll('.whereisit-select__option');
        var groups = dropdown.querySelectorAll('[data-whereisit-group]');

        // Set initial display text from hidden value
        var currentValue = hidden.value;
        if(currentValue){
            options.forEach(function(opt){
                if(opt.dataset.value === currentValue){
                    search.value = opt.textContent.trim();
                }
            });
        }

        search.addEventListener('focus', function(){
            dropdown.classList.add('whereisit-select__dropdown--open');
            search.select();
        });

        search.addEventListener('input', function(){
            var filter = search.value.toLowerCase();

            options.forEach(function(opt){
                var text = opt.textContent.toLowerCase();
                var matches = !filter || text.indexOf(filter) !== -1;
                opt.style.display = matches ? '' : 'none';
            });

            // Hide groups where all options are hidden
            groups.forEach(function(group){
                var visibleOptions = group.querySelectorAll('.whereisit-select__option:not([style*="display: none"])');
                group.style.display = visibleOptions.length > 0 ? '' : 'none';
            });

            dropdown.classList.add('whereisit-select__dropdown--open');
        });

        options.forEach(function(opt){
            opt.addEventListener('click', function(){
                hidden.value = opt.dataset.value;
                search.value = opt.textContent.trim();
                dropdown.classList.remove('whereisit-select__dropdown--open');
            });
        });

        // Close dropdown on outside click
        document.addEventListener('click', function(e){
            if(!container.contains(e.target)){
                dropdown.classList.remove('whereisit-select__dropdown--open');
            }
        });

        // Keyboard navigation
        search.addEventListener('keydown', function(e){
            if(e.key === 'Escape'){
                dropdown.classList.remove('whereisit-select__dropdown--open');
                search.blur();
            }
            if(e.key === 'Enter'){
                e.preventDefault();
                var visible = dropdown.querySelectorAll('.whereisit-select__option:not([style*="display: none"])');
                if(visible.length === 1){
                    visible[0].click();
                }
            }
        });
    }

    function init(){
        initCheckboxes();
        initFilterSelect();
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
