// importing axios
let imported = document.createElement('script');
imported.src = 'https://unpkg.com/axios/dist/axios.min.js';
document.head.appendChild(imported);

const isMobile = window.screen.width < 800;

let mainFormContentElems = document.getElementsByClassName('main-form-content');
let elemToFill = (isMobile) ? mainFormContentElems[3] : mainFormContentElems[1];

elemToFill.innerHTML = `
<form id="mainForm">
            <h4 class="mb-3">Запись на сессию</h4>
            <div class="form-group col-auto">
              <input type="text" class="form-control mb-2 mr-2" placeholder="Имя" name="name">
            </div>
            <div class="form-inline justify-content-center">
              <input type="tel" class="form-control mb-2 mr-2" placeholder="+7 999 999 99 99" name="phone">
              <select class="form-control mb-2 mr-2" id="exampleFormControlSelect" name="session_type">
                <option value="in_place">Очно</option>
                <option value="online">Онлайн</option>
              </select>
            </div>
            <div class="form-group">
              <button class="btn m-auto" type="submit">Записаться</button>
              <small>Отправляя форму Вы соглашаетесь на обработку персональных данных</small>
            </div>
          </form>
`;

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
    let elemThanks = document.createElement('h4');
    elemThanks.className = 'm-5';
    elemThanks.textContent = 'Спасибо за запись!';
    while (elemToFill.lastElementChild) {
        elemToFill.removeChild(elemToFill.lastElementChild);
    }
    elemToFill.appendChild(elemThanks)
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

