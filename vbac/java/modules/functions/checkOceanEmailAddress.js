function checkOceanEmailAddress(email) {
    if (email.search(/ocean/i) != -1) {
        return true;
    } else {
        return false;
    }
}

export { checkOceanEmailAddress as default };