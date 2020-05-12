// importing axios
let imported = document.createElement('script');
imported.src = 'https://unpkg.com/axios/dist/axios.min.js';
document.head.appendChild(imported);

checkName = name => {
    const patternName = /^[a-zA-Zа-яА-Я]+$/;
    return patternName.test(name);
};


checkMobile = mobile => {
    mobile = mobile.replace(/[+\-() \t\n]/g,'');
    const patternMobile = /^[789]*[0-9]{9}$/;
    return patternMobile.test(mobile);
};

transformMobile = mobile => {
    mobile = mobile.replace(/[+\-() \t\n]/g,'');

    if (checkMobile(mobile)) {
        return mobile.length === 11 ? mobile.substring(1) : mobile;
    }
    return null;
};

let form = document.getElementById('mainForm');
let formPhone = form.elements.namedItem('phone');
let formName = form.elements.namedItem('name');

showNotification = function(type, title, message) {
    window.createNotification({
        // close on click
        closeOnClick: true,

        // displays close button
        displayCloseButton: true,

        // nfc-top-left
        // nfc-bottom-right
        // nfc-bottom-left
        positionClass: 'nfc-top-right',

        // callback
        onclick: false,

        // timeout in milliseconds
        showDuration: 3500,

        // success, info, warning, error, and none
        theme: type

    })({
        title: title,
        message: message
    });
};

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

addDangerElement = text => {
    let elemError = document.createElement('div');
    elemError.className = 'alert alert-danger';
    elemError.textContent = text;
    form.appendChild(elemError);
};

changeFormToThanks = function() {
    let formElem = document.getElementsByClassName('main-form-content')[1];
    let elemThanks = document.createElement('h1');
    elemThanks.className = 'm-5';
    elemThanks.textContent = 'Спасибо за запись!';
    while (formElem.lastElementChild) {
        formElem.removeChild(formElem.lastElementChild);
    }
    formElem.appendChild(elemThanks)
};

form.addEventListener('submit', (e) => {

    e.preventDefault();
    let formData = new FormData(e.target);
    formData.set('phone', transformMobile(formData.get('phone')));

    let isCorrectForm = true;
    let type = 'success';
    let title = 'Форма успешно отправлена!';
    let message = 'С Вами свяжутся в бижайшее время';

    if (!checkMobile(formData.get('phone'))) {
        title = 'Телефон указан неверно!';
        message = 'Номер может содержать только 11 или 10 цифр';
        isCorrectForm = false;
    }

    if (!checkName(formData.get('name'))) {
        title = 'Неверное имя!';
        message = 'Имя может содержать только буквы';
        isCorrectForm = false;
    }

    if (!isCorrectForm) {
        type = 'error';
        showNotification(type, title, message);
        return;
    } else {
        changeFormToThanks();
        showNotification(type, title, message);
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

