function xhrPoolManage(jqXHR, settings) {
    $.each(xhrPool, function (idx, jqXHR) {
        jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
        xhrPool.splice(idx, 1);
    });
    xhrPool.push(jqXHR);
}

export { xhrPoolManage as default };