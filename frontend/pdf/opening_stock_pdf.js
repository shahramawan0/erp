// Opening Stock Entry PDF generator using jsPDF + autoTable (matches modal layout)
// Exposes window.generateOpeningStockPdf(voucher)
// 
// Features:
// - Improved multilingual text handling (English + Urdu)
// - Better text rendering for RTL languages
// - Enhanced table formatting and readability
// - Proper text cleaning and normalization
// - Matches the opening stock modal design exactly

(function() {
  function loadScript(url) {
      return new Promise((resolve, reject) => {
        const s = document.createElement('script');
      s.src = url;
      s.async = true;
        s.onload = resolve;
      s.onerror = () => reject(new Error('Failed to load ' + url));
        document.head.appendChild(s);
      });
    }

  async function ensureJsPdfLibs() {
    if (!window.jspdf) {
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
    }
    if (!window.jspdfAutoTable && !window.autoTable && !(window.jspdf && window.jspdf.jsPDF && window.jspdf.jsPDF.API && window.jspdf.jsPDF.API.autoTable)) {
      await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js');
    }
  }

  function imageToPngDataUrl(url) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.crossOrigin = 'anonymous';
      img.onload = function() {
        try {
          const canvas = document.createElement('canvas');
          canvas.width = img.naturalWidth;
          canvas.height = img.naturalHeight;
          canvas.getContext('2d').drawImage(img, 0, 0);
          resolve(canvas.toDataURL('image/png'));
        } catch (e) { reject(e); }
      };
      img.onerror = () => reject(new Error('Logo load failed'));
      img.src = url + (url.includes('?') ? '&' : '?') + 't=' + Date.now();
    });
  }

  function text(doc, txt, x, y, fontSize = 11, opt = {}) {
    if (fontSize) doc.setFontSize(fontSize);
    if (opt.bold) doc.setFont(undefined, 'bold'); else doc.setFont(undefined, 'normal');
    if (opt.color) doc.setTextColor(opt.color[0], opt.color[1], opt.color[2]); else doc.setTextColor(0,0,0);
    
    // Handle multilingual text properly
    if (txt && typeof txt === 'string') {
      // Clean and normalize the text
      txt = txt.replace(/[\u0000-\u001F\u007F-\u009F]/g, ''); // Remove control characters
      txt = txt.trim();
      
      // For very long text, truncate to prevent PDF corruption
      if (txt.length > 100) {
        txt = txt.substring(0, 97) + '...';
      }
    }
    
    doc.text(String(txt == null ? '' : txt), x, y);
  }

  // Helper function to format dates (matches modal format exactly)
  function formatDateForPdf(dateString) {
    if (!dateString) return 'N/A';
    try {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    } catch (error) {
      return dateString;
    }
  }

  // Helper function to create clean multilingual text
  function createMultilingualText(englishText, urduText) {
    if (!englishText && !urduText) return '-';
    if (!urduText) return englishText || '-';
    if (!englishText) return urduText || '-';
    
    // For PDF, we'll use a cleaner format that jsPDF can handle
    // Use line breaks to separate languages instead of slashes
    return (englishText + '\n' + urduText).trim();
  }

  // Enhanced text rendering for multilingual content
  function renderMultilingualText(doc, text, x, y, maxWidth, fontSize = 9) {
    if (!text) return;
    
    doc.setFontSize(fontSize);
    
    // Handle text with line breaks (multilingual)
    if (text.includes('\n')) {
      const lines = text.split('\n');
      let currentY = y;
      
      lines.forEach((line, index) => {
        const cleanLine = line.trim();
        if (cleanLine) {
          // For Urdu text, try to handle RTL better
          if (cleanLine.match(/[\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFF]/)) {
            // Urdu/Arabic text - render with special handling
            try {
              // Use a more compatible approach for RTL text
              doc.text(cleanLine, x, currentY, { 
                align: 'left',
                baseline: 'middle'
              });
            } catch (e) {
              // Fallback: render as simple text
              doc.text(cleanLine, x, currentY);
            }
          } else {
            // English text - render normally
            doc.text(cleanLine, x, currentY);
          }
          currentY += fontSize + 2;
        }
      });
    } else {
      // Single line text
      doc.text(text, x, y);
    }
  }

  async function generateOpeningStockPdf(voucher) {
    await ensureJsPdfLibs();
    const jsPDF = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : window.jsPDF;
    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape orientation
    const pageW = doc.internal.pageSize.getWidth();
    const margin = 12; // 12mm margins like modal spacing
    let y = margin;

    // Header (logo + title) - matches modal header
    try {
      const logo = await imageToPngDataUrl('assets/images/logo/kmi-logo.webp');
      doc.addImage(logo, 'PNG', margin, y, 28, 28 * 0.42);
    } catch (_) {}

    text(doc, 'Opening Stock Voucher', margin + 34, y + 8, 16, { bold: true });
    text(doc, 'Generated by Store Department', margin + 34, y + 14, 10, { color: [100, 116, 139] });

    // Meta right (Voucher # and Date like modal top-right)
    const metaX = pageW - margin - 70;
    const dateStr = formatDateForPdf(voucher.voucher_date);
    const voucherCode = 'Voucher #: ' + (voucher.voucher_no || 'N/A');
    text(doc, voucherCode, metaX, y + 6, 10);
    text(doc, 'Date: ' + dateStr, metaX, y + 12, 10);
    y += 26;

    // Two cards layout (like modal) - Left card: Voucher & Unit
    const cardX = margin;
    const cardW = (pageW - 2 * margin - 10) / 2; // Two cards with gap
    const rowH = 6;

    // Left card: Voucher & Unit details
    const leftCardRows = [ 
      ['Unit', voucher.unit_name || '-'], 
      ['Total Items', voucher.total_items || '-'],
      ['Status', voucher.status || 'Opening']
    ];
    const leftCardY = y;
    const leftCardH = leftCardRows.length * rowH + 6;
    doc.setDrawColor(229, 231, 235); 
    doc.setFillColor(249, 250, 251);
    doc.roundedRect(cardX - 1, leftCardY - 3, cardW, leftCardH, 2, 2, 'FD');
    
    let leftCardYPos = leftCardY + 4; 
    const leftLabelX = cardX + 6; 
    const leftValueX = cardX + 40;
    leftCardRows.forEach((r, i) => { 
      text(doc, r[0] + ':', leftLabelX, leftCardYPos, 11, { bold: true }); 
      text(doc, r[1], leftValueX, leftCardYPos, 11); 
      if (i < leftCardRows.length - 1) { 
        doc.setDrawColor(229,231,235); 
        doc.line(cardX, leftCardYPos + 2, cardX + cardW - 2, leftCardYPos + 2); 
      } 
      leftCardYPos += rowH; 
    });

    // Right card: Quantity & Date details
    const rightCardX = cardX + cardW + 10;
    const rightCardRows = [ 
      ['Total Quantity', voucher.total_qty || '-'], 
      ['Created On', formatDateForPdf(voucher.created_at)],
      ['Generated', new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })]
    ];
    const rightCardY = y;
    const rightCardH = rightCardRows.length * rowH + 6;
    doc.setDrawColor(229, 231, 235); 
    doc.setFillColor(249, 250, 251);
    doc.roundedRect(rightCardX - 1, rightCardY - 3, cardW, rightCardH, 2, 2, 'FD');
    
    let rightCardYPos = rightCardY + 4; 
    const rightLabelX = rightCardX + 6; 
    const rightValueX = rightCardX + 40;
    rightCardRows.forEach((r, i) => { 
      text(doc, r[0] + ':', rightLabelX, rightCardYPos, 11, { bold: true }); 
      text(doc, r[1], rightValueX, rightCardYPos, 11); 
      if (i < rightCardRows.length - 1) { 
        doc.setDrawColor(229,231,235); 
        doc.line(rightCardX, rightCardYPos + 2, rightCardX + cardW - 2, rightCardYPos + 2); 
      } 
      rightCardYPos += rowH; 
    });

    y += Math.max(leftCardH, rightCardH) + 2;

    // Items table (matches modal table structure exactly)
    const head = [['#', 'Item Code', 'Item Name', 'Rack', 'Quantity', 'Description']];

    const body = [];
    (voucher.items || []).forEach((item, idx) => {
      const qty = Math.trunc(parseFloat(item.qty || 0)) || 0;
      
      body.push([
        idx + 1,
        item.item_id || '-',
        item.item_name || '-',
        item.rack_name || '-',
        qty,
        item.narration || '-'
      ]);
    });

    if (doc.autoTable) {
      doc.autoTable({
        startY: y,
        head,
        body,
        margin: { left: margin, right: margin },
        tableWidth: 'auto',
        rowPageBreak: 'auto',
        styles: { 
          fontSize: 10, 
          cellPadding: 3, 
          overflow: 'linebreak',
          lineColor: [229, 231, 235],
          lineWidth: 0.1
        },
        headStyles: { 
          fillColor: [243, 244, 246], 
          textColor: 0, 
          fontSize: 10, 
          cellPadding: 3,
          fontStyle: 'bold'
        },
        columnStyles: {
          0: { halign: 'center', cellWidth: 15 },   // #
          1: { halign: 'center', cellWidth: 25 },   // Item Code
          2: { cellWidth: 'auto', halign: 'left' }, // Item Name
          3: { cellWidth: 'auto', halign: 'left' }, // Rack
          4: { halign: 'center', cellWidth: 25 },   // Quantity
          5: { cellWidth: 'auto', halign: 'left' }  // Description
        }
      });
    } else if (window.jspdfAutoTable) {
      window.jspdfAutoTable(doc, { startY: y, head, body });
    }

    // Footer
    const footerY = doc.internal.pageSize.getHeight() - margin;
    text(doc, 'Printed on ' + new Date().toISOString().slice(0, 16).replace('T', ' '), margin, footerY, 9, { color: [100, 116, 139] });

    const safeCode = (voucher.voucher_no || 'N/A').toString().replace(/[^a-z0-9_\-#]/gi, '_');
    doc.save('OpeningStock_' + safeCode + '.pdf');
  }

  window.generateOpeningStockPdf = generateOpeningStockPdf;
})();
