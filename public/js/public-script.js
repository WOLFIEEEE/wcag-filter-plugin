document.addEventListener("DOMContentLoaded", function() {
    // Handle dropdown toggles
    const dropdowns = [
        { buttonId: 'versionDropdown', menuId: 'versionFilter' },
        { buttonId: 'levelDropdown', menuId: 'levelFilter' },
        { buttonId: 'roleDropdown', menuId: 'roleFilter' },
        { buttonId: 'downloadBtn', menuId: 'downloadOptions' }
    ];

    dropdowns.forEach(dropdown => {
        setupDropdown(dropdown.buttonId, dropdown.menuId);
    });

    fetch('wp-content/plugins/wcag-filter-plugin/data/wcag-data.json')
    .then(response => response.json())
    .then(data => populateTable(data))
    .catch(error => console.error('Error fetching JSON data:', error));

    function populateTable(data) {
        const tableBody = document.querySelector("#wcagTable tbody");
        tableBody.innerHTML = ''; // Clear existing table rows

        data.forEach(item => {
            const row = document.createElement('tr');
            row.dataset.criteria = item.criteria;
            row.dataset.requirement = item.requirement;
            row.dataset.version = item.version;
            row.dataset.level = item.level;
            row.dataset.roles = item.roles.join(',');

            row.innerHTML = `
            <td>${item.criteria}</td>
            <td>${item.requirement}</td>
            <td>${item.level}</td>
            <td class="team-role">
                ${item.roles.map(role => `<span class="role-span">${role}</span>`).join('')}
            </td>
        `;
            tableBody.appendChild(row);
        });

        // Apply filter on data load to match initial checkbox selections
        filterTable();
    }

    function setupDropdown(buttonId, menuId) {
        var button = document.getElementById(buttonId);
        var menu = document.getElementById(menuId);
    
        button.onclick = function (e) {
            e.stopPropagation();
    
            // Check if this specific dropdown is currently active
            var isActive = menu.classList.contains("dropdown-menu--active");
    
            // Close all dropdowns before toggling the current one
            closeAllDropdowns();
    
            // Toggle the current dropdown based on its previous state
            if (!isActive) {
                menu.classList.add("dropdown-menu--active");
                button.setAttribute("aria-expanded", "true");
                focusFirstInteractiveElement(menu);
            } else {
                menu.classList.remove("dropdown-menu--active");
                button.setAttribute("aria-expanded", "false");
            }
        };
    
        // Close dropdown when clicking outside
        document.addEventListener("click", function() {
            closeAllDropdowns();
        });
    
        menu.onclick = function (e) {
            e.stopPropagation(); // Prevent closing when clicking inside
        };
    
        menu.addEventListener('keydown', function(event) {
            if (event.key === 'Tab') {
                // Delay the execution to allow focus to move to the next element
                setTimeout(() => {
                    // Check if the active element is not inside the menu or the button
                    if (!menu.contains(document.activeElement) && document.activeElement !== button) {
                        menu.classList.remove("dropdown-menu--active");
                        button.setAttribute("aria-expanded", "false");
                    }
                }, 0);
            }
        });
        
    
        // Highlight the button if any checkbox is selected
        var checkboxes = menu.querySelectorAll("input[type='checkbox']");
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener("change", function() {

                if (menuId === 'versionFilter') {
                    checkboxes.forEach(box => {
                        if (box !== checkbox) {
                            box.checked = false;
                        }
                    });
                }

                if (Array.from(checkboxes).some(chk => chk.checked)) {
                    button.classList.add("button--highlight");
                } else {
                    button.classList.remove("button--highlight");
                }
                filterTable(); // Apply filter whenever a checkbox changes
            });
        });
    }
    
    function closeAllDropdowns() {
        var activeMenus = document.querySelectorAll(".dropdown-menu--active");
        activeMenus.forEach(function(menu) {
            menu.classList.remove("dropdown-menu--active");
        });
    
        var buttons = document.querySelectorAll(".dropdown-toggle");
        buttons.forEach(function(button) {
            button.setAttribute("aria-expanded", "false");
        });
    }
    

    function focusFirstInteractiveElement(menu) {
        var firstInteractiveElement = menu.querySelector('input, button, select, textarea, a[href]');
        if (firstInteractiveElement) {
            firstInteractiveElement.focus();
        }
    }


    function filterTable() {
        var versionSelected = getSelectedCheckboxes('versionFilter');
        var levelSelected = getSelectedCheckboxes('levelFilter');
        var roleSelected = getSelectedCheckboxes('roleFilter');

        var tableRows = document.querySelectorAll("#wcagTable tbody tr");
        tableRows.forEach(function(row) {
            var version = row.dataset.version;
            var level = row.dataset.level;
            var roles = row.dataset.roles.split(',');

            var versionMatch = versionSelected.length === 0 || versionSelected.includes(version) ||
            (versionSelected.includes('2.1') && version === '2.0') ||
            (versionSelected.includes('2.2') && (version === '2.0' || version === '2.1'));
            var levelMatch = levelSelected.length === 0 || levelSelected.includes(level);
            var roleMatch = roleSelected.length === 0 || roles.some(role => roleSelected.includes(role));

            if (versionMatch && levelMatch && roleMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function getSelectedCheckboxes(filterId) {
        var selectedValues = [];
        var checkboxes = document.querySelectorAll("#" + filterId + " input[type='checkbox']:checked");
        checkboxes.forEach(function(checkbox) {
            selectedValues.push(checkbox.value);
        });
        return selectedValues;
    }

    document.getElementById('downloadExcel').addEventListener('click', function() {
        downloadTableAsExcel('wcagTable', 'WCAG_Criteria.xlsx');
    });

    document.getElementById('downloadPdf').addEventListener('click', function() {
        downloadTableAsPDF('wcagTable', 'WCAG_Criteria.pdf');
    });

    function downloadTableAsExcel(tableID, filename) {
        let table = document.getElementById(tableID);
        let workbook = XLSX.utils.table_to_book(table, {sheet: "Sheet 1"});
        XLSX.writeFile(workbook, filename);
    }

    function downloadTableAsPDF(tableID, filename) {
        let table = document.getElementById(tableID);
        const {jsPDF} = window.jspdf;
        let doc = new jsPDF('p', 'pt', 'a4');
        doc.autoTable({
            html: '#' + tableID,
            theme: 'grid',
            styles: {
                font: 'helvetica',
                fontSize: 10,
                cellPadding: 3,
                overflow: 'linebreak'
            },
            headStyles: {
                fillColor: [22, 160, 133]
            }
        });
        doc.save(filename);
    }
});
