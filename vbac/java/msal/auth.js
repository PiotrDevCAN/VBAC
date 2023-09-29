/*
 * Browser check variables
 * If you support IE, our recommendation is that you sign-in using Redirect APIs
 * If you as a developer are testing using Edge InPrivate mode, please add "isEdge" to the if check
 */
const ua = window.navigator.userAgent;
const msie = ua.indexOf("MSIE ");
const msie11 = ua.indexOf("Trident/");
const msedge = ua.indexOf("Edge/");
const isIE = msie > 0 || msie11 > 0;
const isEdge = msedge > 0;

let signInType;
let accountId = "";

// let headersList = {
//     "Content-Type": "application/x-www-form-urlencoded"
// }

// let bodyContent = "grant_type=client_credentials&client_id=ffa9e035-e345-4ff2-aad3-e84696202171&client_secret=0808Q~V661a2TBuMMaDVyy6wyczWyLmbTO2t.aDN&scope=api://51d4cf5d-b248-4e4f-b6dd-5897e73e247f/.default";

//     // let response = fetch("https://login.microsoftonline.com/f260df36-bc43-424c-8f44-c85226657b01/oauth2/v2.0/token",
//     //     {
//     //         method: "POST",
//     //         mode: "no-cors",
//     //         headers: headersList,
//     //         body: bodyContent
//     //     }
//     // ).then(response => response.text())
//     //     .then(response => console.log(response))
//     //     .catch(err => console.error(err));

// fetch("https://login.microsoftonline.com/f260df36-bc43-424c-8f44-c85226657b01/oauth2/v2.0/token",
// {
//     method: "POST",
//     mode: "no-cors",
//     headers: headersList,
//     body: bodyContent
// }
// )
// .then(response => response.ok ? response.json() : Promise.reject(response))
// .then(json => { alert('all good'); }) //all good

// //next line is optional
// .catch(response => { 
//     alert('handle error');
//     console.log(response);
// }) //handle error

// let data = await response.text();
// alert('works');
// console.log(data);


// $fields = array(
//     'client_id' => $this->config->client_id,
//     'client_secret' => $this->config->client_secret,
//     'grant_type' => 'client_credentials',
//     'scope' => $this->config->token_scope
// );

// Example POST method implementation:
// async function postData(url = "", data = {}) {
//     console.log(url);
//     console.log(data);
//     // Default options are marked with *
//     const response = await fetch(url, {
//         method: "POST", // *GET, POST, PUT, DELETE, etc.
//         mode: "no-cors", // no-cors, *cors, same-origin
//         cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
//         credentials: "same-origin", // include, *same-origin, omit
//         headers: {
//             "Access-Control-Allow-Origin": "http://localhost:8082",
//             "Content-Type": "application/json",
//             // 'Content-Type': 'application/x-www-form-urlencoded',
//         },
//         redirect: "follow", // manual, *follow, error
//         referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
//         body: JSON.stringify(data), // body data type must match "Content-Type" header
//     });
//     console.log(response);
//     return response;
//     // return response.json(); // parses JSON response into native JavaScript objects
// }

// const url = 'https://login.microsoftonline.com/f260df36-bc43-424c-8f44-c85226657b01/oauth2/v2.0/token';
// const data = {
//     client_id: 'ffa9e035-e345-4ff2-aad3-e84696202171',
//     client_secret: '0808Q~V661a2TBuMMaDVyy6wyczWyLmbTO2t.aDN',
//     grant_type: 'client_credentials',
//     scope: 'api://51d4cf5d-b248-4e4f-b6dd-5897e73e247f/.default'
// };

// console.log(JSON.stringify(data));

// // postData(url, data).then((data) => {
// //   console.log('my own token');
// //   console.log(data); // JSON data parsed by `data.json()` call
// // });

// // var options = {
// //     // headers: {
// //     //     // "Access-Control-Allow-Origin": "http://localhost:8082",
// //     //     // "Content-Type": "application/json",
// //     //     "Content-Type": "application/x-www-form-urlencoded"
// //     //     // 'Content-Type': 'application/x-www-form-urlencoded',
// //     // },
// //     // method: 'POST',
// //     // mode: "no-cors", // no-cors, *cors, same-origin
// //     body: JSON.stringify(data)
// //     // body: '{"grant_type":"client_credentials","client_id":"ab8fd819-a2a7-417e-a6bf-0116d8a29ecb","client_secret":"-3Q8Q~6mgkg0KJtOYC3duR4lncHSPF953aOAeaY0","scope":"api://ab8fd819-a2a7-417e-a6bf-0116d8a29ecb/.default"}'
// // };
// const myInit = {
//     method: "POST",
//     // headers: myHeaders,
//     // mode: "cors",
//     // cache: 'default',
//     mode: "no-cors", // no-cors, *cors, same-origin
//     headers: {
//         "Access-Control-Allow-Origin": "*",
//         "Content-Type": "application/x-www-form-urlencoded",
//     },
//     body: new URLSearchParams({
//         'client_id': 'ffa9e035-e345-4ff2-aad3-e84696202171',
//         'client_secret': '0808Q~V661a2TBuMMaDVyy6wyczWyLmbTO2t.aDN',
//         'grant_type': 'client_credentials',
//         'scope': 'api://51d4cf5d-b248-4e4f-b6dd-5897e73e247f/.default'
//     })
// };
// fetch('https://login.microsoftonline.com/f260df36-bc43-424c-8f44-c85226657b01/oauth2/v2.0/token', myInit)
//     .then(response => response.json())
//     .then(response => console.log(response))
//     .catch(err => console.error(err));

// /*
//  * Create the main myMSALObj instance
//  * configuration parameters are located at authConfig.js
//  */
// // const myMSALObj = new msal.PublicClientApplication(msalConfig);

// // Redirect: once login is successful and redirects with tokens, call Graph API
// // myMSALObj.initialize().then(() => {
// //     myMSALObj.handleRedirectPromise().then(handleResponse).catch(err => {
// //         console.error(err);
// //     });
// // });

// function handleResponse(resp) {
//     const isInIframe = window.parent !== window;
//     alert(isInIframe);
//     if (resp !== null) {
//         accountId = resp.account.homeAccountId;
//         alert(resp.account);
//         // showWelcomeMessage(resp.account);
//         // getTokenRedirect(loginRequest, resp.account);
//     } else {
//         alert('make ssoSilent');
//         myMSALObj.ssoSilent(silentRequest).then(() => {
//             const currentAccounts = myMSALObj.getAllAccounts();
//             accountId = currentAccounts[0].homeAccountId;
//             alert(accountId);
//             // showWelcomeMessage(currentAccounts[0]);
//             // getTokenRedirect(loginRequest, currentAccounts[0]);
//         }).catch(error => {
//             console.error("Silent Error: " + error);
//             // if (error instanceof msal.InteractionRequiredAuthError) {
//             //     signIn("loginPopup");
//             // }
//         });
//     }
// }