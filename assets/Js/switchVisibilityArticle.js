const { async } = require("regenerator-runtime");

const switchs = document.querySelectorAll('input[data-actif-id]');

// On recupere tous les inputs

switchs.forEach((element) => {
    // e =  event dans le cas présent
    element.addEventListener('click', (e) => {
        const articleId = e.target.getAttribute('data-actif-id');
        // ou
        // const articleId = e.target.dataset.actifId;
        switchVisibility(articleId);
    });  
});
async function switchVisibility(id){
    const response = fetch(`/admin/article/switch/${id}`);

    if(response.status < 200 || response.status > 299){
        console.log(response);
    }
}