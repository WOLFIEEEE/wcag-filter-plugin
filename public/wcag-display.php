<?php
function wcag_filter_shortcode() {
    ob_start(); ?>
<div class="filter_table_container">
<div class="wcag-filter-container">
        <!-- Filter Buttons Container -->
        <div class="filter-buttons-container">
            <div class="dropdown" role="menu" aria-label="Filter Versions">
                <button class="dropdown-toggle" type="button" id="versionDropdown" aria-haspopup="true" aria-expanded="false" aria-controls="versionFilter">
                    Versions
                </button>
                <ul class="dropdown-menu" id="versionFilter" role="menu" aria-labelledby="versionDropdown">
                    <li role="menuitem"><input type="checkbox" value="2.0" id="version-2-0"><label for="version-2-0">WCAG 2.0</label></li>
                    <li role="menuitem"><input type="checkbox" value="2.1" id="version-2-1"><label for="version-2-1">WCAG 2.1</label></li>
                    <li role="menuitem"><input type="checkbox" value="2.2" id="version-2-2"><label for="version-2-2">WCAG 2.2</label></li>
                </ul>
            </div>
            <div class="dropdown" role="menu" aria-label="Filter Levels">
                <button class="dropdown-toggle" type="button" id="levelDropdown" aria-haspopup="true" aria-expanded="false" aria-controls="levelFilter">
                    Levels
                </button>
                <ul class="dropdown-menu" id="levelFilter" role="menu" aria-labelledby="levelDropdown">
                    <li role="menuitem"><input type="checkbox" value="A" id="level-A"><label for="level-A">Level A</label></li>
                    <li role="menuitem"><input type="checkbox" value="AA" id="level-AA"><label for="level-AA">Level AA</label></li>
                </ul>
            </div>
            <div class="dropdown" role="menu" aria-label="Filter Team Roles">
                <button class="dropdown-toggle" type="button" id="roleDropdown" aria-haspopup="true" aria-expanded="false" aria-controls="roleFilter">
                    Team Roles
                </button>
                <ul class="dropdown-menu" id="roleFilter" role="menu" aria-labelledby="roleDropdown">
                    <li role="menuitem"><input type="checkbox" value="Developer" id="role-Developer"><label for="role-Developer">Developer</label></li>
                    <li role="menuitem"><input type="checkbox" value="Designer" id="role-Designer"><label for="role-Designer">Designer</label></li>
                    <li role="menuitem"><input type="checkbox" value="Content Editor" id="role-ContentEditor"><label for="role-ContentEditor">Content Editor</label></li>
                </ul>
            </div>
        </div>

        <!-- Download Button Container -->
        <div class="download-button-container">
            <button id="downloadBtn" class="download-button" aria-haspopup="true" aria-expanded="false" aria-controls="downloadOptions">
                <i class="fas fa-download" aria-hidden="true"></i> Download
            </button>
            <ul class="dropdown-menu" id="downloadOptions" role="menu" aria-labelledby="downloadBtn">
                <li role="menuitem"><button id="downloadExcel">Download Excel</button> </li>
                <li role="menuitem" ><button id="downloadPdf">Download PDF</button> </li>
            </ul>
        </div>
    </div>

    <table id="wcagTable">
    <thead>
        <tr>
            <th class="criterion-column">Criterion</th>
            <th class="title-column">Title</th>
            <th class="level-column">Level</th>
            <th class="team-roles-column">Team Roles</th>
        </tr>
    </thead>
        <tbody>
            <!-- Table rows will be populated dynamically -->
            <!-- Add more rows as needed -->
        </tbody>
    </table>

</div>


    <?php return ob_get_clean();
}
add_shortcode('wcag_filter', 'wcag_filter_shortcode');
