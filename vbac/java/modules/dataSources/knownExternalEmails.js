/**
 *
 */

let knownExternalEmailsFetch = await cacheBustImport('./modules/dataSources/fetch/knownExternalEmails.js');

let APIData = knownExternalEmailsFetch;

class knownExternalEmails {

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

const KnownExternalEmails = new knownExternalEmails();

export { KnownExternalEmails as default };