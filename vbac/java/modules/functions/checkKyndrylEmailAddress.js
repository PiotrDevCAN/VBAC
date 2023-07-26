function checkKyndrylEmailAddress(email) {
    if (email.search(/kyndryl/i) != -1) {
        return true;
    } else {
        return false;
    }
}

export { checkKyndrylEmailAddress as default };