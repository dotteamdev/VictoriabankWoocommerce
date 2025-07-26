<?php 

function activity_log_add_menu_item() {
    add_menu_page(
      __('Activity Log', 'victoriabank-payment-gateway'),
      __('Activity Log', 'victoriabank-payment-gateway'),
      'manage_options',
      'activity-log',
      'activity_log_render_activity_log',
      'dashicons-list-view'
    );
}

add_action('admin_menu', 'activity_log_add_menu_item');

function activity_log_render_activity_log() {
    $log_file_visa_mastercard = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/vb_visa_mastercard.log';
    $log_file_star_card = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/vb_star_card_rate.log';
    $log_file_puncte_star = ABSPATH . 'wp-content/plugins/vb-payment-plugin/logs/vb_puncte_star.log';

    if(file_exists($log_file_visa_mastercard)) {
      $log_file_data_visa_mastercard = file_get_contents($log_file_visa_mastercard);
      $activities_visa_mastercard = explode("\n", $log_file_data_visa_mastercard);
      unset($activities_visa_mastercard[count($activities_visa_mastercard)-1]);
    } else {
      $activities_visa_mastercard = [];
    }

    if(file_exists($log_file_star_card)) {
      $log_file_data_star_card = file_get_contents($log_file_star_card);
      $activities_star_card = explode("\n", $log_file_data_star_card);
      unset($activities_star_card[count($activities_star_card)-1]);
    } else {
      $activities_star_card = [];
    }

    if(file_exists($log_file_puncte_star)) {
      $log_file_data_puncte_star = file_get_contents($log_file_puncte_star);
      $activities_puncte_star = explode("\n", $log_file_data_puncte_star);
      unset($activities_puncte_star[count($activities_puncte_star)-1]);
    } else {
      $activities_puncte_star = [];
    }

    ?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <select id="tab-select" style="margin-top: 15px">
        <option value="visa-mastercard" selected>Visa/MasterCard</option>
        <option value="star-card">Star Card Rate</option>
        <option value="puncte-star">Puncte star</option>
      </select>

      <div id="visa-mastercard" class="tabcontent" style="border-top: 1px solid #ccc; margin-top: 20px">
        <table id="visa-mastercard-table" class="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th style="width: 165px;">ID</th>
              <th><?php echo __('Activity', 'victoriabank-payment-gateway') ?></th>
              <th style="width: 165px;"><?php echo __('Timestamp', 'victoriabank-payment-gateway') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($activities_visa_mastercard as $key => $activity) { ?>
              <tr>
                <td><?php echo esc_html($key + 1); ?></td>
                <td><?php echo esc_html(explode("]:", $activity)[1]); ?></td>
                <td><?php echo esc_html(get_timestamp($activity)); ?></td>
              </tr>
            <?php } 
              if(count($activities_visa_mastercard) === 0) {
                ?>
                  <tr>
                    <td colspan="3" style="font-size: 16px; text-align: center; height: 100px; vertical-align: middle;"><?php echo __('No log data for this payment type', 'victoriabank-payment-gateway') ?></td>
                  </tr>
                <?php
              }
            ?>
          </tbody>
        </table>
      </div>

      <div id="star-card" class="tabcontent" style="display:none; border-top: 1px solid #ccc; margin-top: 20px">
        <table id="star-card-table" class="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th style="width: 165px;">ID</th>
              <th><?php echo __('Activity', 'victoriabank-payment-gateway'); ?></th>
              <th style="width: 165px;"><?php echo __('Timestamp', 'victoriabank-payment-gateway'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($activities_star_card as $key => $activity) { ?>
              <tr>
                <td><?php echo esc_html($key + 1); ?></td>
                <td><?php echo esc_html(explode("]:", $activity)[1]); ?></td>
                <td><?php echo esc_html(get_timestamp($activity)); ?></td>
              </tr>
              <?php } 
              if(count($activities_star_card) === 0) {
                ?>
                  <tr>
                    <td colspan="3" style="font-size: 16px; text-align: center; height: 100px; vertical-align: middle;"><?php echo __('No log data for this payment type', 'victoriabank-payment-gateway') ?></td>
                  </tr>
                <?php
              }
            ?>
          </tbody>
        </table>
      </div>

      <div id="puncte-star" class="tabcontent" style="display:none; border-top: 1px solid #ccc; margin-top: 20px">
        <table id="puncte-star-table"  class="wp-list-table widefat fixed striped">
          <thead>
            <tr>
              <th style="width: 165px;">ID</th>
              <th><?php echo __('Activity', 'victoriabank-payment-gateway'); ?></th>
              <th style="width: 165px;"><?php echo __('Timestamp', 'victoriabank-payment-gateway'); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($activities_puncte_star as $key => $activity) { ?>
              <tr>
                <td><?php echo esc_html($key + 1); ?></td>
                <td><?php echo esc_html(explode("]:", $activity)[1]); ?></td>
                <td><?php echo esc_html(get_timestamp($activity)); ?></td>
              </tr>
              <?php } 
              if(count($activities_puncte_star) === 0) {
                ?>
                  <tr>
                    <td colspan="3" style="font-size: 16px; text-align: center; height: 100px; vertical-align: middle;"><?php echo __('No log data for this payment type', 'victoriabank-payment-gateway') ?></td>
                  </tr>
                <?php
              }
            ?>
          </tbody>
        </table>
      </div>
      <div id="pagination" style="margin-top: 15px; justify-content: flex-end; display: flex;">
          <button id="prev" style="width: 100px;"><?php echo __('Previous', 'victoriabank-payment-gateway') ?></button>
          <span id="currentPage" style="width: 50px; text-align: center;"></span>
          <button id="next" style="width: 100px;"><?php echo __('Next', 'victoriabank-payment-gateway') ?></button>
      </div>
    </div>
    <script>
      const tabSelect = document.getElementById('tab-select');
      const tabContents = document.querySelectorAll('.tabcontent');
      let selectedTab = 'visa-mastercard';

      tabSelect.addEventListener('change', (event) => {
        selectedTab = event.target.value;

        tabContents.forEach((tab) => {
          tab.style.display = 'none';
        });

        document.getElementById(selectedTab).style.display = 'block';

        resetPagination();
      });

      let currentPage = 1;
      let rowsPerPage = 20;

      let table = document.getElementById(selectedTab + '-table').getElementsByTagName("tbody")[0];
      let rows = table.rows;
      let totalRows = rows.length;
      let totalPages = Math.ceil(totalRows / rowsPerPage);

      function showPage(page) {
        currentPage = page;

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        for (let i = 0; i < totalRows; i++) {
          if (i >= start && i < end) {
            rows[i].style.display = "table-row";
          } else {
            rows[i].style.display = "none";
          }
        }

        document.getElementById("currentPage").textContent = currentPage + " / " + totalPages;
      }

      function goToPrevPage() {
        if (currentPage > 1) {
          showPage(currentPage - 1);
        }
      }

      function goToNextPage() {
        if (currentPage < totalPages) {
          showPage(currentPage + 1);
        }
      }

      function resetPagination() {
        currentPage = 1
        rowsPerPage = 20;

        table = document.getElementById(selectedTab + '-table').getElementsByTagName("tbody")[0];
        rows = table.rows;
        totalRows = rows.length;
        totalPages = Math.ceil(totalRows / rowsPerPage);
        showPage(currentPage);

        if(totalRows < 2) {
          document.getElementById("pagination").style = 'display:none';
        } else {
          document.getElementById("pagination").style = 'margin-top: 15px; justify-content: flex-end; display: flex;';
        }
      }

      document.getElementById("prev").addEventListener("click", goToPrevPage);
      document.getElementById("next").addEventListener("click", goToNextPage);

      showPage(currentPage);
    </script>
  <?php
}

function get_timestamp($activity) {
  preg_match('/\[(.*?)\]/', $activity, $match);
  return $match[1];
}