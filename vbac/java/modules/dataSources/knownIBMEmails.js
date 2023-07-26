/**
 *
 */

let knownIBMEmailsFetch = await cacheBustImport('./modules/dataSources/fetch/knownIBMEmails.js');

let APIData = knownIBMEmailsFetch;

class knownIBMEmails {

    emails = [];

    constructor() {

    }

    async getEmails() {
        // await for API data
        var dataRaw = await APIData.data;
        this.emails = Object.values(dataRaw);
        return this.emails;
    }
}

const KnownIBMEmails = new knownIBMEmails();

export { KnownIBMEmails as default };