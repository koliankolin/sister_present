// importing axios
let imported = document.createElement('script');
imported.src = 'https://unpkg.com/axios/dist/axios.min.js';
document.head.appendChild(imported);

checkName = name => {
    const patternName = /^[a-zA-Zа-яА-Я]+$/;
    return patternName.test(name);
};


checkMobile = mobile => {
    const patternMobile = /^[789][0-9]{10}$/;
    return patternMobile.test(mobile);
};

transformMobile = mobile => {
    mobile = mobile.replace(/[+\-() \t\n]/g,'');

    if (checkMobile(mobile)) {
        return mobile.substring(1)
    }
    return null;
};

let form = document.getElementById('mainForm');
let formPhone = form.elements.namedItem('phone');
let formName = form.elements.namedItem('name');

formPhone.addEventListener('change', e => {
    if (!checkMobile(e.target.value)) {
        formPhone.classList.add('text-danger');
    } else {
        formPhone.classList.remove("text-danger");
    }
});

formName.addEventListener('change', e => {
    if (!checkName(e.target.value)) {
        formName.classList.add('text-danger');
    } else {
        formName.classList.remove("text-danger");
    }
});

form.addEventListener('submit', (e) => {

    e.preventDefault();
    let formData = new FormData(e.target);
    formData.set('phone', transformMobile(formData.get('phone')));

    if (!!formData.get('phone')) {
        let elemError = document.createElement('div');
        elemError.className = 'alert alert-danger';
        elemError.textContent = 'Некорректный номер';
        form.appendChild(elemError);
        return;
    }

    if (!!checkName(formData.get('name'))) {
        let elemError = document.createElement('div');
        elemError.className = 'alert alert-danger';
        elemError.textContent = 'Имя содержит НЕ только буквы';
        form.appendChild(elemError);
        return;
    }
    axios({
        method: 'post',
        url: '/',
        data: formData,
    })
    .then(function (response) {
        //handle success

    })
    .catch(function (response) {
        //handle error
        console.log(response.response);
    });
});

