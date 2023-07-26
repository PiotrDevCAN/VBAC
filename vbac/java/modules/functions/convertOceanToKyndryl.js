/**
 *
 */

let checkOceanEmailAddress = await cacheBustImport('./modules/functions/checkOceanEmailAddress.js');
let checkKyndrylEmailAddress = await cacheBustImport('./modules/functions/checkKyndrylEmailAddress.js');

function convertOceanToKyndryl(email) {
    if (typeof (email) !== 'undefined') {
        if (checkKyndrylEmailAddress(email)) {
            return email;
        } else if (checkOceanEmailAddress(email)){
            return email.toString().replace('ocean.ibm.com', 'kyndryl.com');
        } else {
            return false;
        }
    } else {
        return false;
    }
}

export { convertOceanToKyndryl as default };