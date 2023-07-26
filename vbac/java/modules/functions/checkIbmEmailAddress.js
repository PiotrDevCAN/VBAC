function checkIbmEmailAddress(email) {
    if (email.search(/ibm/i) != -1) {
        return true;
    } else {
        return false;
    }
}

export { checkIbmEmailAddress as default };