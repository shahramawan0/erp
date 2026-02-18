<?php /* Opening Stock Entry modal template used by store_opening_stock.php */ ?>
<script type="text/template" id="opening-stock-view-template">
  <div id="opening-stock-view-wrapper">
    <div class="invoice-actions" style="display:flex; justify-content:flex-end; gap:10px; margin-bottom:20px;">
      <button id="btnPrintOpeningStock" class="modal-action-btn print">
        <i class="material-symbols-outlined">print</i>
        <span>Print</span>
      </button>
      <button id="btnDownloadOpeningStockPdf" class="modal-action-btn pdf">
        <i class="material-symbols-outlined">picture_as_pdf</i>
        <span>PDF</span>
      </button>
    </div>
    <style>
      @page { size: A4 landscape; margin: 12mm; }
      #opening-stock-view{font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; color:#111827; width:100%; margin:0 auto;}
      #opening-stock-view .muted{color:#6b7280; font-weight:400}
      #opening-stock-view .header{display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e5e7eb; padding-bottom:8px; margin-bottom:12px}
      #opening-stock-view .brand{display:flex; align-items:center; gap:12px}
      #opening-stock-view .brand img{height:42px}
      #opening-stock-view .brand .title{font-size:18px; font-weight:700}
      #opening-stock-view .meta{font-size:12px; line-height:1.4}
      #opening-stock-view .grid{display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin:10px 0 14px}
      #opening-stock-view .card{background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:12px}
      #opening-stock-view .info{display:grid; grid-template-columns: 1fr;}
      #opening-stock-view .field{display:grid; grid-template-columns: 120px 1fr; column-gap:8px; align-items:center; padding:6px 0; border-bottom:1px dashed #e5e7eb}
      #opening-stock-view .field:last-child{border-bottom:none}
      #opening-stock-view .label{color:#374151; font-weight:600}
      #opening-stock-view .value{color:#111827}
      #opening-stock-view table{width:100%; border-collapse:collapse; margin-top:6px}
      #opening-stock-view th, #opening-stock-view td{border:1px solid #e5e7eb; padding:8px; font-size:12px;}
      #opening-stock-view th{background:#f3f4f6; text-align:center}
      #opening-stock-view td{text-align:center}
      #opening-stock-view td.num{text-align:right}
      #opening-stock-view .footer{margin-top:12px; font-size:12px; color:#6b7280}
      @media print{ .invoice-actions{display:none !important} @page { size: A4 landscape; margin: 12mm; } }
      .generating-pdf .invoice-actions { display: none !important; }
      .modal-action-btn { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; font-size: 14px; font-weight: 600; border-radius: 8px; border: 1px solid transparent; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); transition: all .2s ease; }
      .modal-action-btn i { font-size: 18px; line-height: 1; }
      .modal-action-btn.print { background: #2563eb; border-color: #2563eb; color: #fff; }
      .modal-action-btn.print:hover { background: #1d4ed8; transform: translateY(-1px); }
      .modal-action-btn.pdf { background: #ef4444; border-color: #ef4444; color: #fff; }
      .modal-action-btn.pdf:hover { background: #dc2626; transform: translateY(-1px); }
    </style>
    <div id="opening-stock-view">
      <div class="header">
        <div class="brand">
          <img src="assets/images/logo/icon.webp" alt="Khawaja Traders" />
          <div>
            <div class="title">Opening Stock Voucher</div>
            <div class="muted">Khawaja Traders - Store Department</div>
          </div>
        </div>
        <div class="meta">
          <div><strong>Voucher #: {{voucher_no}}</strong></div>
          <div><strong>Date:</strong> {{voucher_date}}</div>
        </div>
      </div>
      <div class="grid">
        <div class="card">
          <div class="info">
            <div class="field"><div class="label">Unit</div><div class="value">{{unit_name}}</div></div>
            <div class="field"><div class="label">Total Items</div><div class="value">{{total_items}}</div></div>
            <div class="field"><div class="label">Status</div><div class="value">{{status}}</div></div>
          </div>
        </div>
        <div class="card">
          <div class="info">
            <div class="field"><div class="label">Total Quantity</div><div class="value">{{total_qty}}</div></div>
            <div class="field"><div class="label">Created On</div><div class="value">{{created_at}}</div></div>
            <div class="field"><div class="label">Generated</div><div class="value">{{printed_on}}</div></div>
          </div>
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th style="text-align:center;">#</th>
            <th style="text-align:left;">Item</th>
            <th style="text-align:center;">Rack</th>
            <th style="text-align:center;">Quantity</th>
            <th style="text-align:center;">Description</th>
          </tr>
        </thead>
        <tbody style="text-align:center;">
          __ITEM_ROWS__
        </tbody>
      </table>
      <div class="footer">Printed on {{printed_on}}</div>
    </div>
  </div>
</script>
