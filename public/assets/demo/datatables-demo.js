// Call the dataTables jQuery plugin
$(document).ready(function () {
    $("#dataTable").DataTable({
        lengthMenu: [
            [10, 25, 50, 100, 250, -1],
            [10, 25, 50, 100, 250, "All"],
        ],
    });
});
