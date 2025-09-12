// assets/js/dashboard.js
function makeLine(ctx, labels, data){
  return new Chart(ctx, {
    type: 'line',
    data: { labels, datasets: [{ data, fill:false, tension:.35 }]},
    options:{
      responsive:true,
      plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}}, y:{beginAtZero:true}}
    }
  });
}
function makeBar(ctx, labels, data){
  return new Chart(ctx, {
    type: 'bar',
    data: { labels, datasets: [{ data }]},
    options:{
      responsive:true,
      plugins:{legend:{display:false}},
      scales:{x:{grid:{display:false}}, y:{beginAtZero:true}}
    }
  });
}
window.addEventListener('DOMContentLoaded', ()=>{
  const oc = document.getElementById('ordersChart'); if (oc) makeLine(oc, ordersLabels, ordersData);
  const rc = document.getElementById('revenueChart'); if (rc) makeLine(rc, revenueLabels, revenueData);
  const gc = document.getElementById('gamesChart'); if (gc) makeBar(gc, gameLabels, gameData);
});
