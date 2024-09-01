<?php
function wcag_json_editor_menu() {
    add_menu_page('WCAG Criteria', 'WCAG Criteria', 'manage_options', 'wcag-json-editor', 'wcag_json_editor_page', 'dashicons-edit', 100);
}
add_action('admin_menu', 'wcag_json_editor_menu');

function wcag_json_editor_page() {
    ?>
    <div class="wrap">
        <h1>WCAG JSON Editor</h1>
        <select id="wcag-version-selector">
            <option value="">Select WCAG Version</option>
            <option value="2.0">2.0</option>
            <option value="2.1">2.1</option>
            <option value="2.2">2.2</option>
        </select>
        <div id="wcag-criteria-list">
            <!-- Criteria will be loaded here -->
        </div>
        <button id="save-json-data" class="button-primary">Save Changes</button>
        <?php wp_nonce_field('save_wcag_json', 'wcag_json_nonce'); ?>
    </div>
    <?php
}

add_action('admin_footer', 'wcag_json_editor_scripts');
function wcag_json_editor_scripts() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            const jsonDataUrl = '<?php echo plugin_dir_url(__FILE__) . '../data/wcag-data.json'; ?>';
            let wcagData = [];

            function loadCriteria(version) {
                const filteredData = wcagData.filter(item => item.version === version);
                $('#wcag-criteria-list').empty();

                if (filteredData.length > 0) {
                    const tableHeader = `
                        <div class="wcag-table">
                            <div class="wcag-table-header">
                                <div class="wcag-table-column">Criteria</div>
                                <div class="wcag-table-column">Requirement</div>
                                <div class="wcag-table-column">Roles</div>
                            </div>
                        </div>`;
                    $('#wcag-criteria-list').append(tableHeader);

                    filteredData.forEach((item, index) => {
                        const criteriaHtml = `
                            <div class="wcag-table-row">
                                <div class="wcag-table-column">
                                    <h3> ${item.criteria}</h3>
                                </div>
                                <div class="wcag-table-column">
                                    <textarea data-index="${index}" name="requirement">${item.requirement}</textarea>
                                </div>
                                <div class="wcag-table-column">
                                    <input type="text" data-index="${index}" name="roles" value="${item.roles.join(', ')}" />
                                </div>
                            </div>
                        `;
                        $('#wcag-criteria-list').append(criteriaHtml);
                    });
                } else {
                    $('#wcag-criteria-list').append('<p>No criteria found for this version.</p>');
                }
            }


            $.getJSON(jsonDataUrl, function(data) {
                wcagData = data;
            });

            $('#wcag-version-selector').change(function() {
                const version = $(this).val();
                if (version) {
                    loadCriteria(version);
                } else {
                    $('#wcag-criteria-list').empty();
                }
            });

            $('#save-json-data').click(function() {
                const version = $('#wcag-version-selector').val();
                if (!version) return alert('Please select a version first.');

                $('#wcag-criteria-list .wcag-criteria-item').each(function() {
                    const index = $(this).find('textarea').data('index');
                    wcagData[index].requirement = $(this).find('textarea[name="requirement"]').val();
                    wcagData[index].roles = $(this).find('input[name="roles"]').val().split(',').map(role => role.trim());
                });

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'save_wcag_json',
                        wcag_json_data: JSON.stringify(wcagData),
                        _ajax_nonce: '<?php echo wp_create_nonce('save_wcag_json_nonce'); ?>'
                    },
                    success: function(response) {
                        alert('Data saved successfully.');
                    },
                    error: function() {
                        alert('Failed to save data.');
                    }
                });
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_save_wcag_json', 'wcag_save_json');
function wcag_save_json() {
    check_ajax_referer('save_wcag_json_nonce', '_ajax_nonce');

    $json_file = plugin_dir_path(__FILE__) . '../data/wcag-data.json';
    $json_data = stripslashes($_POST['wcag_json_data']);

    if (file_put_contents($json_file, $json_data)) {
        wp_send_json_success('Data saved successfully.');
    } else {
        wp_send_json_error('Failed to save data.');
    }
}

