import{c as e,i as t,o as n,s as r,t as i,u as a}from"./api-D9V2xFWG.js";import{$ as o,S as s,b as c,c as l,d as u,h as d,i as f,j as p,k as m,l as h,s as g,t as _,tt as v,u as y,w as b}from"./_plugin-vue_export-helper-DXQMzK5J.js";var x={class:`invoice-modal-overlay`},S={class:`invoice-modal-content`},C={class:`no-print invoice-toolbar`},w={class:`toolbar-right`},T=[`value`],E={class:`toolbar-left`},D={id:`invoice-print-target`,class:`invoice-paper`},O={class:`invoice-header-box`},k={class:`header-company-info`},A={class:`comp-logo-box`},j=[`src`],M={key:1,class:`logo-icon-text`},N={class:`comp-title`},P={class:`comp-meta`},F={class:`header-invoice-meta`},I={class:`buyer-bar`},L={class:`buyer-col`},R={class:`buyer-col`},z={class:`invoice-items-table`},B={key:0},V={style:{"text-align":`left`,"font-weight":`bold`}},H={class:`no-print`,style:{"text-align":`center`}},U={style:{"text-align":`center`,"font-weight":`bold`}},ee=[`onUpdate:modelValue`],te={style:{"text-align":`center`}},ne=[`onUpdate:modelValue`],W={style:{"text-align":`left`,"font-weight":`bold`}},G=[`onUpdate:modelValue`],K={class:`no-print`,style:{"text-align":`center`}},q=[`onClick`],J={class:`no-print add-item-bar`},Y={class:`invoice-total-row`},X={class:`total-number`},Z={class:`invoice-notes-box`},Q=_({__name:`InvoiceModal`,props:{invoice:{type:Object,required:!0}},emits:[`close`],setup(e){let t=e,i=p(``),a=p(``),o=p(null),c=p(`هزینه فوق پس از تحویل و تست سلامت آسانسور قابل پرداخت می‌باشد.`),d=()=>{i.value&&(t.invoice.items.push({title:i.value,part:a.value||`-`,amount:Number(o.value||0)}),i.value=``,a.value=``,o.value=null)},h=e=>{t.invoice.items.splice(e,1)},_=g(()=>{let e=t.invoice.hasServiceFee?Number(t.invoice.serviceFeeAmount||0):0;return(t.invoice.items||[]).forEach(t=>e+=Number(t.amount||0)),e}),Q=()=>{let e=t.invoice.selectedCompany||{},n=e.logo_url||e.logo_path?`<img src="${e.logo_url||`https://service123.ir/uploads/`+e.logo_path}" style="width:40px;height:40px;object-fit:contain;" />`:`<span style="font-size:22px;">${e.logo_icon||`🛗`}</span>`,r=``,i=1;t.invoice.hasServiceFee&&(r+=`
      <tr>
        <td style="text-align:center;font-weight:bold;">${i++}</td>
        <td style="font-weight:bold;">سرویس دوره‌ای آسانسور و آچارکشی</td>
        <td style="text-align:center;">-</td>
        <td style="text-align:left;font-weight:bold;font-family:monospace;">${Number(t.invoice.serviceFeeAmount||0).toLocaleString(`fa-IR`)}</td>
      </tr>
    `),(t.invoice.items||[]).forEach(e=>{r+=`
      <tr>
        <td style="text-align:center;font-weight:bold;">${i++}</td>
        <td style="font-weight:bold;">${e.title||``}</td>
        <td style="text-align:center;">${e.part||`-`}</td>
        <td style="text-align:left;font-weight:bold;font-family:monospace;">${Number(e.amount||0).toLocaleString(`fa-IR`)}</td>
      </tr>
    `});let a=c.value?`
    <div style="border:1.2px solid #000000;padding:6px 10px;border-radius:4px;margin-bottom:8px;font-size:10px;color:#000000;">
      <strong>توضیحات: </strong>${c.value.replace(/\n/g,`<br>`)}
    </div>
  `:``,o=`
    <!DOCTYPE html>
    <html dir="rtl" lang="fa">
    <head>
      <meta charset="utf-8">
      <title>فاکتور_${t.invoice.invoiceNo||`A5`}</title>
      <style>
        @page { size: A5 portrait; margin: 6mm 8mm; }
        * { box-sizing: border-box; }
        body {
          font-family: Tahoma, 'Vazirmatn', sans-serif;
          direction: rtl;
          margin: 0;
          padding: 0;
          background: #ffffff;
          color: #000000;
          font-size: 10px;
          line-height: 1.4;
        }
        .header-box {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          border-bottom: 2px solid #000000;
          padding-bottom: 6px;
          margin-bottom: 8px;
        }
        .comp-info { display: flex; align-items: center; gap: 8px; }
        .logo-box {
          width: 44px;
          height: 44px;
          border: 1.5px solid #000000;
          border-radius: 6px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .comp-title { font-size: 13px; font-weight: 900; margin: 0; color: #000000; }
        .comp-meta { font-size: 9.5px; color: #000000; font-weight: bold; margin: 2px 0 0 0; }
        .meta-box {
          border: 1.5px solid #000000;
          padding: 5px 8px;
          border-radius: 6px;
          font-size: 10px;
          min-width: 140px;
          line-height: 1.7;
          color: #000000;
        }
        .meta-box div { display: flex; justify-content: space-between; }
        .buyer-bar {
          display: flex;
          justify-content: space-between;
          border: 1.5px solid #000000;
          padding: 6px 10px;
          border-radius: 6px;
          margin-bottom: 8px;
          font-size: 10.5px;
          color: #000000;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 10px; color: #000000; }
        th, td { border: 1.2px solid #000000; padding: 5px 6px; color: #000000; }
        th { background: #e2e8f0; font-weight: 900; }
        .total-row {
          display: flex;
          justify-content: space-between;
          border: 1.5px solid #000000;
          padding: 6px 10px;
          font-weight: 900;
          margin-bottom: 8px;
          font-size: 11px;
          color: #000000;
          background: #f8fafc;
        }
        .signatures {
          display: flex;
          justify-content: space-between;
          gap: 14px;
          text-align: center;
          font-size: 10px;
          margin-top: 14px;
        }
        .sig-col {
          flex: 1;
          border: 1.5px dashed #000000;
          padding: 6px 8px 30px 8px;
          border-radius: 6px;
          font-weight: bold;
          color: #000000;
        }
      </style>
    </head>
    <body>
      <div class="header-box">
        <div class="comp-info">
          <div class="logo-box">${n}</div>
          <div>
            <h2 class="comp-title">${e.name||`سامانه فنی service123`}</h2>
            <p class="comp-meta">ثبت: ${e.reg_number||`-`} | تلفن: ${e.phone||`-`}</p>
          </div>
        </div>
        <div class="meta-box">
          <div><span>شماره:</span> <b>${t.invoice.invoiceNo||`-`}</b></div>
          <div><span>تاریخ:</span> <b>${t.invoice.invoiceDate||`-`}</b></div>
          <div><span>دوره:</span> <b>${t.invoice.periodMonth||`-`}</b></div>
        </div>
      </div>

      <div class="buyer-bar">
        <div><span>طرف قرارداد:</span> <b>${t.invoice.customerName||`-`}</b></div>
        <div><span>مدیریت / نماینده:</span> <b>${t.invoice.contactPerson||`-`}</b></div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width: 28px; text-align: center;">#</th>
            <th>شرح خدمات و اقلام</th>
            <th style="width: 80px; text-align: center;">قطعه</th>
            <th style="width: 105px; text-align: left;">مبلغ (تومان)</th>
          </tr>
        </thead>
        <tbody>
          ${r}
        </tbody>
      </table>

      <div class="total-row">
        <span>جمع کل صورت‌حساب:</span>
        <span style="font-family: monospace;">${_.value.toLocaleString(`fa-IR`)} تومان</span>
      </div>

      ${a}

      <div class="signatures">
        <div class="sig-col">مهر و امضای شرکت مجری</div>
        <div class="sig-col">امضای نماینده / مدیریت ساختمان</div>
      </div>
    </body>
    </html>
  `;if(window.AndroidPrinter&&window.AndroidPrinter.printHtml)window.AndroidPrinter.printHtml(o,`فاکتور_`+(t.invoice.invoiceNo||`A5`));else{let e=document.createElement(`iframe`);e.style.position=`fixed`,e.style.right=`0`,e.style.bottom=`0`,e.style.width=`0`,e.style.height=`0`,e.style.border=`0`,document.body.appendChild(e);let t=e.contentWindow.document;t.open(),t.write(o),t.close(),setTimeout(()=>{e.contentWindow.focus(),e.contentWindow.print(),setTimeout(()=>document.body.removeChild(e),1e3)},300)}};return(t,p)=>(s(),u(`div`,x,[l(`div`,S,[l(`div`,C,[l(`div`,w,[p[13]||=l(`span`,{class:`toolbar-title`},`🧾 فاکتور رسمی (A5)`,-1),m(l(`select`,{"onUpdate:modelValue":p[0]||=t=>e.invoice.selectedCompany=t,class:`comp-select`},[(s(!0),u(f,null,b(e.invoice.companies,e=>(s(),u(`option`,{key:e.id,value:e},v(e.name),9,T))),128))],512),[[n,e.invoice.selectedCompany]])]),l(`div`,E,[l(`button`,{onClick:Q,class:`btn-print-action`},`🖨️ چاپ / PDF`),l(`button`,{onClick:p[1]||=e=>t.$emit(`close`),class:`btn-close-modal`},`✕ بستن`)])]),l(`div`,D,[l(`div`,O,[l(`div`,k,[l(`div`,A,[e.invoice.selectedCompany?.logo_url||e.invoice.selectedCompany?.logo_path?(s(),u(`img`,{key:0,src:e.invoice.selectedCompany.logo_url||`https://service123.ir/uploads/`+e.invoice.selectedCompany.logo_path,alt:`Logo`,class:`logo-img`},null,8,j)):(s(),u(`span`,M,v(e.invoice.selectedCompany?.logo_icon||`🛗`),1))]),l(`div`,null,[l(`h2`,N,v(e.invoice.selectedCompany?.name||`سامانه فنی service123`),1),l(`p`,P,` ثبت: `+v(e.invoice.selectedCompany?.reg_number||`-`)+` | تلفن: `+v(e.invoice.selectedCompany?.phone||`-`),1)])]),l(`div`,F,[l(`div`,null,[p[14]||=l(`span`,null,`شماره:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":p[2]||=t=>e.invoice.invoiceNo=t,class:`editable-meta-input font-mono font-bold`},null,512),[[r,e.invoice.invoiceNo]])]),l(`div`,null,[p[15]||=l(`span`,null,`تاریخ:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":p[3]||=t=>e.invoice.invoiceDate=t,class:`editable-meta-input font-mono font-bold`},null,512),[[r,e.invoice.invoiceDate]])]),l(`div`,null,[p[16]||=l(`span`,null,`دوره:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":p[4]||=t=>e.invoice.periodMonth=t,class:`editable-meta-input font-bold`},null,512),[[r,e.invoice.periodMonth]])])])]),l(`div`,I,[l(`div`,L,[p[17]||=l(`span`,null,`طرف قرارداد:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":p[5]||=t=>e.invoice.customerName=t,class:`editable-buyer-input`},null,512),[[r,e.invoice.customerName]])]),l(`div`,R,[p[18]||=l(`span`,null,`مدیریت / نماینده:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":p[6]||=t=>e.invoice.contactPerson=t,class:`editable-buyer-input`},null,512),[[r,e.invoice.contactPerson]])])]),l(`table`,z,[p[22]||=l(`thead`,null,[l(`tr`,null,[l(`th`,{style:{width:`28px`,"text-align":`center`}},`#`),l(`th`,null,`شرح خدمات و اقلام`),l(`th`,{style:{width:`85px`,"text-align":`center`}},`قطعه`),l(`th`,{style:{width:`110px`,"text-align":`left`}},`مبلغ (تومان)`),l(`th`,{class:`no-print`,style:{width:`32px`,"text-align":`center`}},`حذف`)])],-1),l(`tbody`,null,[e.invoice.hasServiceFee?(s(),u(`tr`,B,[p[19]||=l(`td`,{style:{"text-align":`center`,"font-weight":`bold`}},`۱`,-1),p[20]||=l(`td`,{style:{"font-weight":`bold`}},`سرویس دوره‌ای آسانسور و آچارکشی`,-1),p[21]||=l(`td`,{style:{"text-align":`center`}},`-`,-1),l(`td`,V,[m(l(`input`,{type:`number`,"onUpdate:modelValue":p[7]||=t=>e.invoice.serviceFeeAmount=t,class:`inline-price-input`},null,512),[[r,e.invoice.serviceFeeAmount,void 0,{number:!0}]])]),l(`td`,H,[l(`button`,{onClick:p[8]||=t=>e.invoice.hasServiceFee=!1,class:`btn-row-del`},`✕`)])])):y(``,!0),(s(!0),u(f,null,b(e.invoice.items,(t,n)=>(s(),u(`tr`,{key:n},[l(`td`,U,v(e.invoice.hasServiceFee?n+2:n+1),1),l(`td`,null,[m(l(`input`,{type:`text`,"onUpdate:modelValue":e=>t.title=e,class:`inline-text-input font-bold`},null,8,ee),[[r,t.title]])]),l(`td`,te,[m(l(`input`,{type:`text`,"onUpdate:modelValue":e=>t.part=e,class:`inline-part-input`},null,8,ne),[[r,t.part]])]),l(`td`,W,[m(l(`input`,{type:`number`,"onUpdate:modelValue":e=>t.amount=e,class:`inline-price-input`},null,8,G),[[r,t.amount,void 0,{number:!0}]])]),l(`td`,K,[l(`button`,{onClick:e=>h(n),class:`btn-row-del`},`✕`,8,q)])]))),128))])]),l(`div`,J,[m(l(`input`,{type:`text`,"onUpdate:modelValue":p[9]||=e=>i.value=e,placeholder:`شرح خدمت جدید...`,class:`flex-input text-dark`},null,512),[[r,i.value]]),m(l(`input`,{type:`text`,"onUpdate:modelValue":p[10]||=e=>a.value=e,placeholder:`نام قطعه...`,class:`part-input text-dark`},null,512),[[r,a.value]]),m(l(`input`,{type:`number`,"onUpdate:modelValue":p[11]||=e=>o.value=e,placeholder:`مبلغ (تومان)`,class:`price-input text-dark`},null,512),[[r,o.value,void 0,{number:!0}]]),l(`button`,{onClick:d,class:`btn-add-item`},`➕ افزودن`)]),l(`div`,Y,[p[23]||=l(`span`,null,`جمع کل صورت‌حساب:`,-1),l(`span`,X,v(_.value.toLocaleString(`fa-IR`))+` تومان`,1)]),l(`div`,Z,[p[24]||=l(`div`,{class:`notes-header`},`توضیحات و شرایط پرداخت:`,-1),m(l(`textarea`,{"onUpdate:modelValue":p[12]||=e=>c.value=e,rows:`2`,class:`notes-textarea`,placeholder:`مثال: هزینه فوق پس از تست نهایی قابل پرداخت است. شماره کارت: ...`},null,512),[[r,c.value]])]),p[25]||=l(`div`,{class:`invoice-signatures`},[l(`div`,{class:`sig-col`},`مهر و امضای شرکت مجری`),l(`div`,{class:`sig-col`},`امضای نماینده / مدیریت ساختمان`)],-1)])])]))}},[[`__scopeId`,`data-v-b45dda66`]]),re={class:`invoice-modal-overlay`},ie={class:`invoice-modal-content a4-content`},ae={class:`no-print invoice-toolbar`},oe={class:`toolbar-right`},se=[`value`],ce={class:`toolbar-left`},le={id:`block-report-print-target`,class:`invoice-paper`},ue={class:`invoice-header-box`},de={class:`header-company-info`},fe={class:`comp-logo-box`},pe=[`src`],me={key:1,class:`logo-icon-text`},he={class:`comp-title`},ge={class:`comp-meta`},_e={class:`header-invoice-meta`},ve={class:`section-block`},ye={class:`report-table`},be={class:`col-num`},xe={class:`col-building`},Se=[`onUpdate:modelValue`],Ce={class:`col-status`},we=[`onUpdate:modelValue`],Te={class:`col-date`},Ee=[`onUpdate:modelValue`],De={class:`col-desc`},Oe=[`onUpdate:modelValue`],ke={class:`section-block mt-3`},Ae={class:`report-table`},je={class:`col-num`},Me={class:`col-building`},Ne=[`onUpdate:modelValue`],Pe={class:`col-desc`},Fe=[`onUpdate:modelValue`],Ie={class:`col-part`},Le=[`onUpdate:modelValue`],Re={class:`col-cost`},ze=[`onUpdate:modelValue`],Be={class:`no-print col-del`},Ve=[`onClick`],He={key:0},Ue={class:`invoice-signatures`},We={class:`sig-col`},Ge=_({__name:`BlockReportModal`,props:{report:{type:Object,required:!0}},emits:[`close`],setup(e){let t=e,i=()=>{t.report.breakdowns.push({building:``,title:``,part:`-`,cost:`0`})},a=e=>{t.report.breakdowns.splice(e,1)},o=()=>{let e=t.report.selectedCompany||{},n=e.logo_url||e.logo_path?`<img src="${e.logo_url||`https://service123.ir/uploads/`+e.logo_path}" style="width:46px;height:46px;object-fit:contain;" />`:`<span style="font-size:24px;">${e.logo_icon||`🛗`}</span>`,r=``;(t.report.buildings||[]).forEach(e=>{r+=`
      <tr>
        <td style="text-align:center;">${e.idx}</td>
        <td style="font-weight:bold;">${e.title||``}</td>
        <td style="text-align:center;font-weight:bold;">${e.status||``}</td>
        <td style="text-align:center;font-family:monospace;font-weight:bold;">${e.date||``}</td>
        <td>${e.note||``}</td>
      </tr>
    `});let i=``;!t.report.breakdowns||t.report.breakdowns.length===0?i=`<tr><td colspan="5" style="text-align:center;padding:10px;font-weight:bold;">خرابی ثبت‌شده‌ای در این دوره وجود ندارد.</td></tr>`:t.report.breakdowns.forEach((e,t)=>{i+=`
        <tr>
          <td style="text-align:center;">${t+1}</td>
          <td>${e.building||``}</td>
          <td>${e.title||``}</td>
          <td style="text-align:center;">${e.part||`-`}</td>
          <td style="text-align:left;font-weight:bold;font-family:monospace;">${e.cost||`0`}</td>
        </tr>
      `});let a=`
    <!DOCTYPE html>
    <html dir="rtl" lang="fa">
    <head>
      <meta charset="utf-8">
      <title>گزارش_ماهانه_${t.report.locationTitle||`A4`}</title>
      <style>
        @page { size: A4 portrait; margin: 8mm 10mm; }
        * { box-sizing: border-box; }
        body {
          font-family: Tahoma, 'Vazirmatn', sans-serif;
          direction: rtl;
          margin: 0;
          padding: 0;
          background: #ffffff;
          color: #000000;
          font-size: 11px;
          line-height: 1.4;
        }
        .header-box {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          border-bottom: 2px solid #000000;
          padding-bottom: 8px;
          margin-bottom: 12px;
        }
        .comp-info { display: flex; align-items: center; gap: 10px; }
        .logo-box {
          width: 50px;
          height: 50px;
          border: 1.5px solid #000000;
          border-radius: 6px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .comp-title { font-size: 14px; font-weight: 900; margin: 0; color: #000000; }
        .comp-meta { font-size: 10.5px; color: #000000; margin: 3px 0 0 0; font-weight: bold; }
        .meta-box {
          border: 1.5px solid #000000;
          padding: 6px 12px;
          border-radius: 6px;
          font-size: 11px;
          min-width: 180px;
          line-height: 1.8;
          color: #000000;
        }
        .meta-box div { display: flex; justify-content: space-between; }
        .section-title {
          font-weight: 900;
          font-size: 11.5px;
          border-bottom: 1.5px solid #000000;
          padding: 3px 0;
          margin: 12px 0 6px 0;
          color: #000000;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px; color: #000000; }
        th, td { border: 1.2px solid #000000; padding: 6px 8px; color: #000000; }
        th { background: #e2e8f0; font-weight: 900; }
        .signatures {
          display: flex;
          justify-content: space-between;
          gap: 20px;
          text-align: center;
          font-size: 11px;
          margin-top: 24px;
        }
        .sig-col {
          flex: 1;
          border: 1.5px dashed #000000;
          padding: 8px 10px 42px 10px;
          border-radius: 6px;
          font-weight: bold;
          color: #000000;
        }
      </style>
    </head>
    <body>
      <div class="header-box">
        <div class="comp-info">
          <div class="logo-box">${n}</div>
          <div>
            <h2 class="comp-title">${e.name||`سامانه مدیریت فنی service123`}</h2>
            <p class="comp-meta">کاربرگ سرویس و نگهداری دوره‌ای و گزارش فنی آسانسور</p>
            <p class="comp-meta">شماره ثبت: ${e.reg_number||`-`} | تلفن: ${e.phone||`-`}</p>
          </div>
        </div>
        <div class="meta-box">
          <div><span>محدوده / بلوک:</span> <b>${t.report.locationTitle||`-`}</b></div>
          <div><span>دوره سرویس:</span> <b>${t.report.serviceMonth||`-`}</b></div>
          <div><span>تاریخ تنظیم:</span> <b>${t.report.reportDate||`-`}</b></div>
        </div>
      </div>

      <div class="section-title">۱. وضعیت سرویس دوره‌ای ورودی‌های بلوک</div>
      <table>
        <thead>
          <tr>
            <th style="width: 32px; text-align: center;">#</th>
            <th style="width: 120px;">ورودی / آسانسور</th>
            <th style="width: 80px; text-align: center;">وضعیت</th>
            <th style="width: 85px; text-align: center;">تاریخ</th>
            <th>شرح اقدامات انجام‌شده</th>
          </tr>
        </thead>
        <tbody>
          ${r}
        </tbody>
      </table>

      <div class="section-title">۲. خرابی‌های رفع‌شده و قطعات مصرفی ماه</div>
      <table>
        <thead>
          <tr>
            <th style="width: 32px; text-align: center;">#</th>
            <th style="width: 120px;">ورودی / واحد</th>
            <th>شرح خرابی و عملیات رفع عیب</th>
            <th style="width: 90px; text-align: center;">قطعه مصرفی</th>
            <th style="width: 100px; text-align: left;">هزینه (تومان)</th>
          </tr>
        </thead>
        <tbody>
          ${i}
        </tbody>
      </table>

      <div class="signatures">
        <div class="sig-col">تایید و امضای نماینده / مدیریت بلوک</div>
        <div class="sig-col">مهر و امضای مسئول سرویس و نگهداری (${e.brand||`service123`})</div>
      </div>
    </body>
    </html>
  `;if(window.AndroidPrinter&&window.AndroidPrinter.printHtml)window.AndroidPrinter.printHtml(a,`گزارش_ماهانه_`+(t.report.locationTitle||`A4`));else{let e=document.createElement(`iframe`);e.style.position=`fixed`,e.style.right=`0`,e.style.bottom=`0`,e.style.width=`0`,e.style.height=`0`,e.style.border=`0`,document.body.appendChild(e);let t=e.contentWindow.document;t.open(),t.write(a),t.close(),setTimeout(()=>{e.contentWindow.focus(),e.contentWindow.print(),setTimeout(()=>document.body.removeChild(e),1e3)},300)}};return(t,c)=>(s(),u(`div`,re,[l(`div`,ie,[l(`div`,ae,[l(`div`,oe,[c[5]||=l(`span`,{class:`toolbar-title`},`📋 کاربرگ رسمی سرویس ماهانه بلوک (A4)`,-1),m(l(`select`,{"onUpdate:modelValue":c[0]||=t=>e.report.selectedCompany=t,class:`comp-select`},[(s(!0),u(f,null,b(e.report.companies,e=>(s(),u(`option`,{key:e.id,value:e},v(e.name),9,se))),128))],512),[[n,e.report.selectedCompany]])]),l(`div`,ce,[l(`button`,{onClick:o,class:`btn-print-action`},`🖨️ چاپ برگه / PDF`),l(`button`,{onClick:c[1]||=e=>t.$emit(`close`),class:`btn-close-modal`},`✕ بستن`)])]),l(`div`,le,[l(`div`,ue,[l(`div`,de,[l(`div`,fe,[e.report.selectedCompany?.logo_url||e.report.selectedCompany?.logo_path?(s(),u(`img`,{key:0,src:e.report.selectedCompany.logo_url||`https://service123.ir/uploads/`+e.report.selectedCompany.logo_path,alt:`Logo`,class:`logo-img`},null,8,pe)):(s(),u(`span`,me,v(e.report.selectedCompany?.logo_icon||`🛗`),1))]),l(`div`,null,[l(`h2`,he,v(e.report.selectedCompany?.name||`سامانه مدیریت فنی service123`),1),c[6]||=l(`p`,{class:`comp-meta`},`کاربرگ سرویس و نگهداری دوره‌ای و گزارش فنی آسانسور`,-1),l(`p`,ge,`شماره ثبت: `+v(e.report.selectedCompany?.reg_number||`-`)+` | تلفن: `+v(e.report.selectedCompany?.phone||`-`),1)])]),l(`div`,_e,[l(`div`,null,[c[7]||=l(`span`,null,`محدوده / بلوک:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":c[2]||=t=>e.report.locationTitle=t,class:`editable-meta-input font-bold`},null,512),[[r,e.report.locationTitle]])]),l(`div`,null,[c[8]||=l(`span`,null,`دوره سرویس:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":c[3]||=t=>e.report.serviceMonth=t,class:`editable-meta-input font-bold`},null,512),[[r,e.report.serviceMonth]])]),l(`div`,null,[c[9]||=l(`span`,null,`تاریخ تنظیم:`,-1),m(l(`input`,{type:`text`,"onUpdate:modelValue":c[4]||=t=>e.report.reportDate=t,class:`editable-meta-input font-mono font-bold`},null,512),[[r,e.report.reportDate]])])])]),l(`div`,ve,[c[11]||=l(`div`,{class:`table-section-title`},` ۱. وضعیت سرویس دوره‌ای ورودی‌های بلوک `,-1),l(`table`,ye,[c[10]||=l(`thead`,null,[l(`tr`,null,[l(`th`,{class:`col-num`},`#`),l(`th`,{class:`col-building`},`ورودی / آسانسور`),l(`th`,{class:`col-status`},`وضعیت`),l(`th`,{class:`col-date`},`تاریخ`),l(`th`,{class:`col-desc`},`شرح اقدامات انجام‌شده`)])],-1),l(`tbody`,null,[(s(!0),u(f,null,b(e.report.buildings,e=>(s(),u(`tr`,{key:e.idx},[l(`td`,be,v(e.idx),1),l(`td`,xe,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.title=t,class:`inline-input font-bold`},null,8,Se),[[r,e.title]])]),l(`td`,Ce,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.status=t,class:`inline-input status-text font-bold`},null,8,we),[[r,e.status]])]),l(`td`,Te,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.date=t,class:`inline-input font-mono text-center font-bold`},null,8,Ee),[[r,e.date]])]),l(`td`,De,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.note=t,class:`inline-input`},null,8,Oe),[[r,e.note]])])]))),128))])])]),l(`div`,ke,[l(`div`,{class:`table-section-title-flex`},[c[12]||=l(`span`,null,`۲. خرابی‌های رفع‌شده و قطعات مصرفی ماه`,-1),l(`button`,{onClick:i,class:`no-print btn-add-row`},`➕ سطر جدید`)]),l(`table`,Ae,[c[14]||=l(`thead`,null,[l(`tr`,null,[l(`th`,{class:`col-num`},`#`),l(`th`,{class:`col-building`},`ورودی / واحد`),l(`th`,{class:`col-desc`},`شرح خرابی و عملیات رفع عیب`),l(`th`,{class:`col-part`},`قطعه مصرفی`),l(`th`,{class:`col-cost`},`هزینه (تومان)`),l(`th`,{class:`no-print col-del`},`حذف`)])],-1),l(`tbody`,null,[(s(!0),u(f,null,b(e.report.breakdowns,(e,t)=>(s(),u(`tr`,{key:t},[l(`td`,je,v(t+1),1),l(`td`,Me,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.building=t,class:`inline-input`},null,8,Ne),[[r,e.building]])]),l(`td`,Pe,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.title=t,class:`inline-input`},null,8,Fe),[[r,e.title]])]),l(`td`,Ie,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.part=t,class:`inline-input text-center`},null,8,Le),[[r,e.part]])]),l(`td`,Re,[m(l(`input`,{type:`text`,"onUpdate:modelValue":t=>e.cost=t,class:`inline-input font-mono text-left font-bold`},null,8,ze),[[r,e.cost]])]),l(`td`,Be,[l(`button`,{onClick:e=>a(t),class:`btn-row-del`},`✕`,8,Ve)])]))),128)),e.report.breakdowns.length===0?(s(),u(`tr`,He,[...c[13]||=[l(`td`,{colspan:`6`,class:`empty-table-cell`},`خرابی ثبت‌شده‌ای وجود ندارد.`,-1)]])):y(``,!0)])])]),l(`div`,Ue,[c[15]||=l(`div`,{class:`sig-col`},`تایید و امضای نماینده / مدیریت بلوک`,-1),l(`div`,We,`مهر و امضای مسئول سرویس و نگهداری (`+v(e.report.selectedCompany?.brand||`service123`)+`) `,1)])])])]))}},[[`__scopeId`,`data-v-db00acf1`]]),Ke={class:`buildings-module`},qe={class:`section-top-bar`},Je={class:`form-row`},Ye={class:`form-col`},Xe={class:`form-col`},Ze={class:`form-col`},Qe={key:1,class:`empty-msg`},$e={key:2,class:`empty-msg`},et=[`onClick`],tt={class:`loc-info-title`},nt={key:0,class:`tag-code font-mono`},rt={class:`count-badge`},it=[`onClick`],at=[`onClick`],ot=[`onClick`],st=[`onClick`],ct={class:`accordion-body`},lt=[`onSubmit`],ut={class:`form-row`},dt=[`onUpdate:modelValue`],ft=[`onUpdate:modelValue`],pt=[`onUpdate:modelValue`],mt=[`onSubmit`],ht={class:`form-title-green`},gt={class:`form-row`},_t=[`onUpdate:modelValue`],vt=[`onUpdate:modelValue`],yt=[`onUpdate:modelValue`],bt={class:`checkbox-row mt-2`},xt=[`id`,`onUpdate:modelValue`],St=[`for`],Ct=[`onUpdate:modelValue`],wt={class:`buildings-list mt-3`},Tt={class:`building-header-row`},Et={class:`text-amber-300`},Dt={key:0,class:`code-pill font-mono`},Ot={key:1,class:`fee-badge`},kt={key:2,class:`address-text`},At={class:`building-btns`},jt=[`onClick`],Mt=[`onClick`],Nt=[`onClick`],Pt=[`onClick`],Ft=[`onClick`],It=[`onSubmit`],Lt={class:`form-row`},Rt=[`onUpdate:modelValue`],zt=[`onUpdate:modelValue`],Bt=[`onUpdate:modelValue`],$={class:`checkbox-row mt-2`},Vt=[`id`,`onUpdate:modelValue`],Ht=[`for`],Ut=[`onUpdate:modelValue`],Wt={class:`contacts-grid mt-2`},Gt={class:`contact-name`},Kt={class:`contact-role`},qt={class:`contact-left`},Jt=[`href`],Yt=[`onClick`],Xt=[`onSubmit`],Zt={class:`form-row-3`},Qt=[`onUpdate:modelValue`],$t=[`onUpdate:modelValue`],en=[`onUpdate:modelValue`],tn={key:0,class:`empty-sub`},nn={__name:`BuildingsTab`,setup(n){let d=p(!0),g=p([]),_=p([]),x=p(!1),S=p({title:``,code:``,address:``}),C=p(!1),w=p({}),T=p(!1),E=p({}),D=async()=>{let e=new Set(g.value.filter(e=>e.isOpen).map(e=>e.id));d.value=!0;try{let t=await i.get(`/admin/dashboard`);t.data&&(_.value=t.data.companies||[],g.value=(t.data.locations||[]).map((t,n)=>({...t,isOpen:e.size>0?e.has(t.id):n===0,showEdit:!1,showNewBuilding:!1,editData:{title:t.title,code:t.code||``,address:t.address||``},newBuildingData:{title:``,code:``,address:``,has_monthly_fee:!1,monthly_fee:0},buildings:(t.buildings||[]).map(e=>({...e,showEdit:!1,showAddContact:!1,editData:{title:e.title,code:e.code||``,address:e.address||``,has_monthly_fee:!!e.has_monthly_fee,monthly_fee:e.monthly_fee||0},newContact:{name:``,role_title:``,phone:``},contacts:e.contacts||[]}))})))}catch(e){console.error(`Fetch locations error:`,e)}finally{d.value=!1}};c(()=>{D()});let O=e=>{e.isOpen=!e.isOpen},k=e=>{e.showEdit=!e.showEdit},A=e=>{e.showNewBuilding=!e.showNewBuilding},j=e=>{e.showEdit=!e.showEdit},M=e=>{e.showAddContact=!e.showAddContact},N=async()=>{try{await i.post(`/admin/locations`,S.value),x.value=!1,S.value={title:``,code:``,address:``},await D()}catch{alert(`خطا در ثبت بلوک`)}},P=async e=>{try{await i.post(`/admin/locations/${e.id}`,e.editData),e.showEdit=!1,await D()}catch{alert(`خطا در ویرایش بلوک`)}},F=async e=>{if(confirm(`آیا از حذف بلوک "${e.title}" و ورودی‌های آن مطمئن هستید؟`))try{await i.post(`/admin/locations/${e.id}/delete`),await D()}catch{alert(`خطا در حذف بلوک`)}},I=async e=>{try{await i.post(`/admin/buildings`,{...e.newBuildingData,location_id:e.id}),e.showNewBuilding=!1,e.newBuildingData={title:``,code:``,address:``,has_monthly_fee:!1,monthly_fee:0},await D()}catch{alert(`خطا در ثبت ورودی`)}},L=async e=>{try{await i.post(`/admin/buildings/${e.id}`,{title:e.editData.title,code:e.editData.code,address:e.editData.address,has_monthly_fee:e.editData.has_monthly_fee,monthly_fee:e.editData.monthly_fee}),e.showEdit=!1,alert(`اطلاعات ورودی با موفقیت ذخیره شد.`),await D()}catch{alert(`خطا در ذخیره ورودی`)}},R=async e=>{if(confirm(`آیا از حذف ورودی "${e.title}" مطمئن هستید؟`))try{await i.post(`/admin/buildings/${e.id}/delete`),await D()}catch{alert(`خطا در حذف ورودی`)}},z=async e=>{try{let t=await i.post(`/admin/buildings/${e.id}/contacts`,e.newContact);t.data&&t.data.contact&&(e.contacts||=[],e.contacts.push(t.data.contact)),e.showAddContact=!1,e.newContact={name:``,role_title:``,phone:``},alert(`مشخصات مدیر با موفقیت ثبت شد.`)}catch{alert(`خطا در ثبت مدیر`)}},B=async(e,t)=>{if(confirm(`آیا از حذف مخاطب "${e.name}" مطمئن هستید؟`))try{await i.post(`/admin/contacts/${e.id}/delete`),t.contacts=t.contacts.filter(t=>t.id!==e.id)}catch{alert(`خطا در حذف مخاطب`)}},V=e=>{let t=_.value||[],n=e.has_monthly_fee?Number(e.monthly_fee||0):0,r=e.contacts&&e.contacts.length>0?e.contacts[0]:null;w.value={building:e,companies:t,selectedCompany:t[0]||{name:`سامانه فنی service123`,reg_number:`-`,phone:`-`},invoiceNo:`${e.id}-${new Date().toISOString().slice(2,10).replace(/-/g,``)}`,invoiceDate:new Intl.DateTimeFormat(`fa-IR`).format(new Date),periodMonth:new Intl.DateTimeFormat(`fa-IR`,{month:`long`,year:`numeric`}).format(new Date),customerName:`${e.location?.title||``} - ${e.title}`,contactPerson:r?`${r.name} (${r.role_title})`:`مدیریت محترم ساختمان`,hasServiceFee:!!e.has_monthly_fee,serviceFeeAmount:n,items:(e.breakdowns||[]).map(e=>({title:e.title,part:e.part_name||`-`,amount:Number(e.amount||0)})),newItemTitle:``,newItemPart:``,newItemAmount:0},C.value=!0},H=e=>{let t=_.value||[],n=[];(e.buildings||[]).forEach(e=>{(e.breakdowns||[]).forEach(t=>{n.push({building:`${e.title} ${e.code?`(`+e.code+`)`:``}`,title:t.title,part:t.part_name||`-`,cost:Number(t.amount||0).toLocaleString(`fa-IR`)})})}),E.value={location:e,locationTitle:e.title,companies:t,selectedCompany:t[0]||{name:`سامانه فنی service123`,brand:`service123`},reportDate:new Intl.DateTimeFormat(`fa-IR`).format(new Date),serviceMonth:new Intl.DateTimeFormat(`fa-IR`,{month:`long`,year:`numeric`}).format(new Date),buildings:(e.buildings||[]).map((e,t)=>({idx:t+1,title:`${e.title} ${e.code?`(`+e.code+`)`:``}`,status:`✔ انجام شد`,date:new Intl.DateTimeFormat(`fa-IR`).format(new Date),note:`سرویس ماهانه، نظافت و آچارکشی انجام شد.`})),breakdowns:n},T.value=!0},U=e=>{window.open(`https://service123.ir/admin/export/pdf/${e.id}`,`_blank`)};return(n,i)=>(s(),u(`div`,Ke,[l(`div`,qe,[i[7]||=l(`div`,null,[l(`h2`,{class:`section-heading`},`🏢 مدیریت بلوک‌ها و ورودی‌ها`),l(`p`,{class:`section-sub`},`برای مشاهده یا مخفی‌سازی ورودی‌ها، روی نوار هر بلوک کلیک کنید`)],-1),l(`button`,{onClick:i[0]||=e=>x.value=!x.value,class:`btn-primary-action`},` ➕ تعریف بلوک جدید `)]),x.value?(s(),u(`form`,{key:0,onSubmit:a(N,[`prevent`]),class:`edit-form-box mb-3`},[i[11]||=l(`div`,{class:`form-title-amber`},`تعریف بلوک / محدوده جدید`,-1),l(`div`,Je,[l(`div`,Ye,[i[8]||=l(`label`,null,`نام بلوک یا فاز:`,-1),m(l(`input`,{"onUpdate:modelValue":i[1]||=e=>S.value.title=e,type:`text`,required:``,placeholder:`مثال: بلوک B3 یا شهرک بیمه`},null,512),[[r,S.value.title]])]),l(`div`,Xe,[i[9]||=l(`label`,null,`کد شناسه (اختیاری):`,-1),m(l(`input`,{"onUpdate:modelValue":i[2]||=e=>S.value.code=e,type:`text`,placeholder:`مثال: B3`},null,512),[[r,S.value.code]])])]),l(`div`,Ze,[i[10]||=l(`label`,null,`آدرس پایه محدوده:`,-1),m(l(`input`,{"onUpdate:modelValue":i[3]||=e=>S.value.address=e,type:`text`,placeholder:`مثال: شهرک اکباتان، فاز ۲`},null,512),[[r,S.value.address]])]),i[12]||=l(`button`,{type:`submit`,class:`btn-save-approve`},`ثبت بلوک`,-1)],32)):y(``,!0),d.value?(s(),u(`div`,Qe,`در حال بارگذاری اطلاعات بلوک‌ها...`)):g.value.length===0?(s(),u(`div`,$e,`هیچ بلوکی تعریف نشده است.`)):y(``,!0),(s(!0),u(f,null,b(g.value,n=>(s(),u(`div`,{key:n.id,class:`loc-accordion-card`},[l(`div`,{class:`loc-header-bar`,onClick:e=>O(n)},[l(`div`,tt,[l(`span`,{class:o([`accordion-arrow`,n.isOpen?`arrow-open`:``])},`◀`,2),i[13]||=l(`span`,{class:`tag-loc`},`محدوده`,-1),l(`strong`,null,v(n.title),1),n.code?(s(),u(`span`,nt,v(n.code),1)):y(``,!0),l(`span`,rt,`(`+v(n.buildings?.length||0)+` ورودی)`,1)]),l(`div`,{class:`loc-actions`,onClick:i[4]||=a(()=>{},[`stop`])},[l(`button`,{onClick:e=>H(n),class:`btn-sm btn-amber`,title:`کاربرگ و گزارش رسمی ماهانه بلوک`},` 📄 گزارش ماهانه `,8,it),l(`button`,{onClick:e=>A(n),class:`btn-sm btn-green`},`➕ ورودی`,8,at),l(`button`,{onClick:e=>k(n),class:`btn-sm btn-edit`},`✏`,8,ot),l(`button`,{onClick:e=>F(n),class:`btn-sm btn-del`},`🗑`,8,st)])],8,et),m(l(`div`,ct,[n.showEdit?(s(),u(`form`,{key:0,onSubmit:a(e=>P(n),[`prevent`]),class:`edit-form-box mt-2`},[l(`div`,ut,[m(l(`input`,{"onUpdate:modelValue":e=>n.editData.title=e,type:`text`,required:``},null,8,dt),[[r,n.editData.title]]),m(l(`input`,{"onUpdate:modelValue":e=>n.editData.code=e,type:`text`,placeholder:`کد`},null,8,ft),[[r,n.editData.code]])]),m(l(`input`,{"onUpdate:modelValue":e=>n.editData.address=e,type:`text`,placeholder:`آدرس پایه`,class:`mt-2`},null,8,pt),[[r,n.editData.address]]),i[14]||=l(`button`,{type:`submit`,class:`btn-save-approve mt-2`},`ذخیره ویرایش بلوک`,-1)],40,lt)):y(``,!0),n.showNewBuilding?(s(),u(`form`,{key:1,onSubmit:a(e=>I(n),[`prevent`]),class:`edit-form-box mt-2`},[l(`div`,ht,`افزودن ورودی / ساختمان به `+v(n.title),1),l(`div`,gt,[m(l(`input`,{"onUpdate:modelValue":e=>n.newBuildingData.title=e,type:`text`,required:``,placeholder:`عنوان ورودی (مثال: ورودی ۵)`},null,8,_t),[[r,n.newBuildingData.title]]),m(l(`input`,{"onUpdate:modelValue":e=>n.newBuildingData.code=e,type:`text`,placeholder:`پلاک یا کد`},null,8,vt),[[r,n.newBuildingData.code]])]),m(l(`input`,{"onUpdate:modelValue":e=>n.newBuildingData.address=e,type:`text`,placeholder:`ادامه آدرس دقیق ساختمان`,class:`mt-2`},null,8,yt),[[r,n.newBuildingData.address]]),l(`div`,bt,[m(l(`input`,{type:`checkbox`,id:`fee_`+n.id,"onUpdate:modelValue":e=>n.newBuildingData.has_monthly_fee=e},null,8,xt),[[t,n.newBuildingData.has_monthly_fee]]),l(`label`,{for:`fee_`+n.id},`شارژ ماهانه دارد`,8,St),n.newBuildingData.has_monthly_fee?m((s(),u(`input`,{key:0,"onUpdate:modelValue":e=>n.newBuildingData.monthly_fee=e,type:`number`,placeholder:`مبلغ شارژ (تومان)`,class:`fee-input`},null,8,Ct)),[[r,n.newBuildingData.monthly_fee,void 0,{number:!0}]]):y(``,!0)]),i[15]||=l(`button`,{type:`submit`,class:`btn-save-approve mt-2`},`ثبت ورودی`,-1)],40,mt)):y(``,!0),l(`div`,wt,[(s(!0),u(f,null,b(n.buildings,e=>(s(),u(`div`,{key:e.id,class:`building-item-card`},[l(`div`,Tt,[l(`div`,null,[l(`strong`,Et,`🏢 `+v(e.title),1),e.code?(s(),u(`span`,Dt,`(`+v(e.code)+`)`,1)):y(``,!0),e.has_monthly_fee?(s(),u(`span`,Ot,` شارژ: `+v(Number(e.monthly_fee||0).toLocaleString())+` ت `,1)):y(``,!0),e.address?(s(),u(`p`,kt,`📍 `+v(e.address),1)):y(``,!0)]),l(`div`,At,[l(`button`,{onClick:t=>V(e),class:`btn-sm btn-invoice`,title:`صدور فاکتور رسمی A5`},` 🧾 فاکتور A5 `,8,jt),l(`button`,{onClick:t=>U(e),class:`btn-sm btn-ledger`,title:`صورت‌وضعیت مالی و مانده‌حساب`},` 📊 صورت‌حساب `,8,Mt),l(`button`,{onClick:t=>M(e),class:`btn-sm btn-dark`},`📞 ثبت مدیر`,8,Nt),l(`button`,{onClick:t=>j(e),class:`btn-sm btn-edit`},`✏`,8,Pt),l(`button`,{onClick:t=>R(e),class:`btn-sm btn-del`},`🗑`,8,Ft)])]),e.showEdit?(s(),u(`form`,{key:0,onSubmit:a(t=>L(e),[`prevent`]),class:`edit-form-box mt-2`},[l(`div`,Lt,[m(l(`input`,{"onUpdate:modelValue":t=>e.editData.title=t,type:`text`,required:``},null,8,Rt),[[r,e.editData.title]]),m(l(`input`,{"onUpdate:modelValue":t=>e.editData.code=t,type:`text`,placeholder:`کد`},null,8,zt),[[r,e.editData.code]])]),m(l(`input`,{"onUpdate:modelValue":t=>e.editData.address=t,type:`text`,placeholder:`آدرس دقیق`,class:`mt-2`},null,8,Bt),[[r,e.editData.address]]),l(`div`,$,[m(l(`input`,{type:`checkbox`,id:`edit_fee_`+e.id,"onUpdate:modelValue":t=>e.editData.has_monthly_fee=t},null,8,Vt),[[t,e.editData.has_monthly_fee]]),l(`label`,{for:`edit_fee_`+e.id},`شارژ ماهانه`,8,Ht),e.editData.has_monthly_fee?m((s(),u(`input`,{key:0,"onUpdate:modelValue":t=>e.editData.monthly_fee=t,type:`number`,class:`fee-input`},null,8,Ut)),[[r,e.editData.monthly_fee,void 0,{number:!0}]]):y(``,!0)]),i[16]||=l(`button`,{type:`submit`,class:`btn-save-approve mt-2`},`ذخیره ورودی`,-1)],40,It)):y(``,!0),l(`div`,Wt,[(s(!0),u(f,null,b(e.contacts,t=>(s(),u(`div`,{key:t.id,class:`contact-pill`},[l(`div`,null,[l(`span`,Gt,v(t.name),1),l(`span`,Kt,`(`+v(t.role_title)+`)`,1)]),l(`div`,qt,[l(`a`,{href:`tel:`+t.phone,class:`contact-phone font-mono`},v(t.phone),9,Jt),l(`button`,{onClick:n=>B(t,e),class:`btn-del-contact`},`✕`,8,Yt)])]))),128))]),e.showAddContact?(s(),u(`form`,{key:1,onSubmit:a(t=>z(e),[`prevent`]),class:`edit-form-box mt-2`},[l(`div`,Zt,[m(l(`input`,{"onUpdate:modelValue":t=>e.newContact.name=t,type:`text`,required:``,placeholder:`نام مدیر`},null,8,Qt),[[r,e.newContact.name]]),m(l(`input`,{"onUpdate:modelValue":t=>e.newContact.role_title=t,type:`text`,required:``,placeholder:`سمت (مدیر، سرایدار)`},null,8,$t),[[r,e.newContact.role_title]]),m(l(`input`,{"onUpdate:modelValue":t=>e.newContact.phone=t,type:`text`,required:``,placeholder:`تلفن همراه`},null,8,en),[[r,e.newContact.phone]])]),i[17]||=l(`button`,{type:`submit`,class:`btn-save-approve mt-2`},`ثبت مشخصات مدیر`,-1)],40,Xt)):y(``,!0)]))),128)),n.buildings?.length?y(``,!0):(s(),u(`div`,tn,` هنوز ورودی داخل این بلوک تعریف نشده است. `))])],512),[[e,n.isOpen]])]))),128)),C.value?(s(),h(Q,{key:3,invoice:w.value,onClose:i[5]||=e=>C.value=!1},null,8,[`invoice`])):y(``,!0),T.value?(s(),h(Ge,{key:4,report:E.value,onClose:i[6]||=e=>T.value=!1},null,8,[`report`])):y(``,!0)]))}},rn={class:`buildings-view-container`},an={__name:`BuildingsView`,setup(e){return(e,t)=>(s(),u(`div`,rn,[d(nn)]))}};export{an as default};