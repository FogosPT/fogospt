$.ajaxSetup({ headers: { "FPTSC": "xw2gfca9l7" } });

$(document).ready(function () {

    var id = $('#myChart').data('id');

    plot(id);
    if (typeof photos === 'function') photos(id);

    function plot(id) {
        var url = 'https://source.fogos.pt/fires/data?id=' + id
        $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                if (data.success && data.data.length) {
                    fogosResourcesChart(document.getElementById('myChart'), data.data, { maintainAspectRatio: false })
                } else {
                    fogosResourcesChartEmpty('#info')
                }
            }
        })

    }
})