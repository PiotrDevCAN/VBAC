async function cacheBustImport(moduleName) {
    return await import(`${moduleName}?${VERSION}`)
        .then(module => {
            // console.log(moduleName + ' ' + VERSION +' module loaded');
            // console.log(module);
            return module.default;
        })
        .catch(err => {
            console.error(moduleName + ' ' + VERSION +' unable to load module');
            console.log(err);
        });
}

// make this module global
console.log('make "cacheBustImport" module global');
window.cacheBustImport = cacheBustImport;

export { cacheBustImport as default };