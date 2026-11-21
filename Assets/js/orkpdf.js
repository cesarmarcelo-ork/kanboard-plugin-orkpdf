(function () {
'use strict';
function init() {
 // The print page does not contain the configuration form.
 // Register the print action first, without relying on inline event handlers.
 var printButton=document.getElementById('orkpdf-print-button');
 if(printButton) printButton.addEventListener('click',function(event){
   event.preventDefault();
   window.print();
 });
 var form=document.getElementById('orkpdf-form');if(!form)return;
 var select=document.getElementById('orkpdf-project'),task=document.getElementById('orkpdf-task'),modeInputs=form.querySelectorAll('input[name="type"]');
 var fields=[].slice.call(form.querySelectorAll('[data-field]')),grid=document.getElementById('orkpdf-sort-grid');
 var title=document.getElementById('orkpdf-title'),ori=document.getElementById('orkpdf-orientation'),density=document.getElementById('orkpdf-density');
 var configKey='orkpdf:config:v020';
 var i18n={};try{i18n=JSON.parse(document.getElementById('orkpdf-root').getAttribute('data-i18n')||'{}');}catch(e){}
 function tr(k, fallback){return i18n[k]||fallback;}
 function mode(){var el=form.querySelector('input[name="type"]:checked');return el?el.value:'detail';}
 function config(){return {fields:fields.filter(function(x){return x.checked}).map(function(x){return x.dataset.field}),order:[].slice.call(grid.children).map(function(x){return x.dataset.key}),orientation:ori.value,density:density.value,title:title.value,showDate:document.getElementById('orkpdf-show-date').checked};}
 function setConfig(c){if(!c)return;fields.forEach(function(x){if(Array.isArray(c.fields))x.checked=c.fields.indexOf(x.dataset.field)!==-1;});if(Array.isArray(c.order))c.order.forEach(function(k){var x=grid.querySelector('[data-key="'+k+'"]');if(x)grid.appendChild(x);});ori.value=c.orientation==='landscape'?'landscape':'portrait';density.value=c.density==='compact'?'compact':'normal';title.value=c.title||'';document.getElementById('orkpdf-show-date').checked=c.showDate!==false;refresh();}
 function saveLocal(k,v){try{localStorage.setItem(k,JSON.stringify(v));}catch(e){}}
 function readLocal(k,d){try{return JSON.parse(localStorage.getItem(k))||d;}catch(e){return d;}}
 function url(){var p=form.dataset.urlPattern||'';return p.replace('999999',encodeURIComponent(select.value||'999999'));}
 function refresh(){var c=config(),detail=mode()==='detail';document.getElementById('orkpdf-task-field').hidden=!detail;task.required=detail;document.getElementById('orkpdf-fields-panel').hidden=!detail;
 form.action=url();document.getElementById('orkpdf-selected-fields').value=c.fields.join(',');document.getElementById('orkpdf-field-order').value=c.order.concat(['description','subtasks','comments']).join(',');document.getElementById('orkpdf-date-value').value=c.showDate?'1':'0';
 document.getElementById('orkpdf-preview-sheet').className='orkpdf-preview-sheet '+(c.orientation==='landscape'?'landscape ':'')+(c.density==='compact'?'compact':'');
 document.getElementById('orkpdf-preview-title').textContent=c.title|| (detail?tr('detail_title','Detailed task report'):tr('inventory_title','Task inventory'));document.getElementById('orkpdf-preview-project').textContent=select.options[select.selectedIndex]?.textContent||'';
 var meta=document.getElementById('orkpdf-preview-meta');meta.textContent='';if(detail){c.order.filter(function(k){return c.fields.indexOf(k)!==-1;}).forEach(function(k){var el=grid.querySelector('[data-key="'+k+'"]');if(!el)return;var box=document.createElement('div');box.className='orkpdf-preview-cell';var strong=document.createElement('strong');strong.textContent=el.querySelector('span:last-child').textContent;var v=document.createElement('span');v.textContent='...';box.append(strong,v);meta.appendChild(box);});}
 else {var box=document.createElement('div');box.className='orkpdf-preview-inventory';box.textContent=tr('inventory_columns','ID  |  Title  |  Reference  |  Active');meta.appendChild(box);}
 var sections=document.getElementById('orkpdf-preview-sections');sections.textContent='';if(detail)[['description',tr('description','Full description')],['subtasks',tr('subtasks','Subtasks')],['comments',tr('comments','Comment history')]].forEach(function(a){if(c.fields.indexOf(a[0])===-1)return;var d=document.createElement('div');d.className='orkpdf-preview-section';d.textContent=a[1];sections.appendChild(d);});
 saveLocal(configKey,c);
 }
 var dragging=null;grid.addEventListener('dragstart',function(e){dragging=e.target.closest('.orkpdf-sort-field');if(dragging)dragging.classList.add('dragging');});grid.addEventListener('dragover',function(e){e.preventDefault();var over=e.target.closest('.orkpdf-sort-field');if(dragging&&over&&dragging!==over){var rect=over.getBoundingClientRect();grid.insertBefore(dragging,e.clientY<rect.top+rect.height/2?over:over.nextSibling);}});grid.addEventListener('dragend',function(){if(dragging)dragging.classList.remove('dragging');dragging=null;refresh();});
 form.addEventListener('change',refresh);form.addEventListener('input',refresh);
 document.getElementById('orkpdf-select-all').addEventListener('click',function(){fields.forEach(function(x){x.checked=true;});refresh();});document.getElementById('orkpdf-clear-all').addEventListener('click',function(){fields.forEach(function(x){x.checked=false;});refresh();});
 form.addEventListener('submit',function(e){refresh();if(!select.value){e.preventDefault();select.focus();return;}if(mode()==='detail'&&(!task.value||Number(task.value)<=0)){e.preventDefault();task.reportValidity();return;} /* Native GET form submission: no JavaScript navigation dependency */});
 setConfig(readLocal(configKey,null));refresh();
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
