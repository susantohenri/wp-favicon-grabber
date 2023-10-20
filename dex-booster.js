const dt = new DataTable(jQuery(`[id="henri-dex-booster"]`), {
    serverSide: true,
    processing: true,
    ajax: {
        url: dex_booster.arbitrum_json_url,
        type: `POST`
    }
})