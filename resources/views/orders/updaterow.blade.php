<script>

    function updateRowOrder(rowid)
    {
        let row = $("#" + rowid);
    }

    function updateRowItem(rowid)
    {
        if (rowid.substr(0, 4) != 'tr_I') return;
        let row = $("#" + rowid);
    }

</script>