const dt = new DataTable(jQuery(`[id="henri-dex-booster"]`), {
    serverSide: true,
    ajax: {
        url: `wp-json/dex-booster/v1/arbitrum`,
        type: `POST`
    }
})