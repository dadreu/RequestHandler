document.addEventListener('DOMContentLoaded', function () {
    const calendarBody = document.getElementById('calendar-body');
    const currentMonthElement = document.getElementById('current-month');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const timeSlotsContainer = document.getElementById('time-slots');
    const confirmButtonContainer = document.getElementById('confirm-button-container');

    // Проверка наличия необходимых элементов
    if (!calendarBody || !currentMonthElement || !prevMonthButton || !nextMonthButton || !timeSlotsContainer || !confirmButtonContainer) {
        console.error('Один из элементов не найден на странице.');
        return;
    }

    let currentDate = new Date(2025, 2, 1); // Март 2025
    let selectedDay = null; // Выбранный день
    let selectedTime = null; // Выбранное время

    // Функция для генерации слотов времени с 9:00 до 18:00 с интервалом 15 минут
    function generateTimeSlots() {
        if (!timeSlotsContainer) {
            console.error('Элемент time-slots не найден.');
            return;
        }

        timeSlotsContainer.innerHTML = '';
        for (let hour = 9; hour <= 18; hour++) {
            for (let minute = 0; minute < 60; minute += 15) {
                if (hour === 18 && minute > 0) break; // Останавливаемся на 18:00
                const time = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                const timeSlot = document.createElement('div');
                timeSlot.classList.add('time-slot');
                timeSlot.textContent = time;
                timeSlot.addEventListener('click', () => {
                    if (selectedTime) selectedTime.classList.remove('selected');
                    selectedTime = timeSlot;
                    timeSlot.classList.add('selected');
                    updateConfirmButtonState();
                });
                timeSlotsContainer.appendChild(timeSlot);
            }
        }
    }

    // Функция для отрисовки календаря
    function renderCalendar() {
        if (!calendarBody) {
            console.error('Элемент calendar-body не найден.');
            return;
        }

        calendarBody.innerHTML = '';
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        currentMonthElement.textContent = new Intl.DateTimeFormat('ru-RU', {
            year: 'numeric',
            month: 'long'
        }).format(currentDate);

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Понедельник как первый день недели

        let date = 1;
        for (let i = 0; i < 6; i++) {
            for (let j = 0; j < 7; j++) {
                const day = document.createElement('div');
                if (i === 0 && j < startDay) {
                    day.classList.add('calendar-day', 'empty');
                } else if (date > lastDay.getDate()) {
                    day.classList.add('calendar-day', 'empty');
                } else {
                    day.classList.add('calendar-day');
                    day.textContent = date;
                    if ([10, 11, 12, 13, 14, 17, 18, 19, 20, 21, 24, 25, 26, 27, 28].includes(date)) {
                        day.innerHTML = `<strong>${date}</strong>`;
                    }
                    day.addEventListener('click', () => {
                        if (selectedDay) selectedDay.classList.remove('selected');
                        selectedDay = day;
                        day.classList.add('selected');
                        updateConfirmButtonState();
                    });
                    date++;
                }
                calendarBody.appendChild(day);
            }
        }
    }

    // Функция для обновления состояния кнопки "Записаться"
    function updateConfirmButtonState() {
        if (!confirmButtonContainer) {
            console.error('Элемент confirm-button-container не найден.');
            return;
        }

        if (selectedDay && selectedTime) {
            confirmButtonContainer.innerHTML = `
                <button class="confirm-button" onclick="window.location.href='appointment.html'">Записаться</button>
            `;
        } else {
            confirmButtonContainer.innerHTML = `
                <button class="confirm-button" disabled>Записаться</button>
            `;
        }
    }


    // Переключение месяцев
    prevMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });
    

    // Инициализация
    generateTimeSlots();
    renderCalendar();
});



    // Функция для перехода на date-selection.html и сохранения источника перехода
    function goToDateSelection(sourcePage) {
        sessionStorage.setItem("previousPage", sourcePage);
        window.location.href = "date-selection.html";
    }

    // Функция для возврата на предыдущую страницу
    function goBack() {
        let previousPage = sessionStorage.getItem("previousPage") || "clientReq.html"; // По умолчанию возвращаем на clientReq
        window.location.href = previousPage;
    }
    function saveServices() {
        alert("Настройки услуг сохранены.");
    }
    
    // Функция для добавления новой услуги
    function addService() {
        alert("Добавление новой услуги.");
    }
    // Функция для обнуления значения поля ввода
function resetField(fieldId) {
    document.getElementById(fieldId).value = '';
}

// Функция для сохранения услуги
function saveService() {
    // Получаем значения полей
    const name = document.getElementById('service-name').value;
    const duration = document.getElementById('service-duration').value;
    const price = document.getElementById('service-price').value;

    // Проверка на пустые поля
    if (name === '' || duration === '' || price === '') {
        alert("Пожалуйста, заполните все поля.");
        return;
    }

    // Здесь можно добавить код для отправки данных на сервер или локальное сохранение
    alert("Услуга сохранена.");
}

document.addEventListener("DOMContentLoaded", function () {
    const masterId = localStorage.getItem("master_id"); // Получаем ID мастера

    if (masterId) {
        fetch(`get_master_appointments.php?master_id=${masterId}`)
            .then(response => response.json())
            .then(data => {
                const recordGroup = document.querySelector(".record-group");

                if (data.success) {
                    let days = {}; // Объект для группировки записей по дням недели

                    // Обработка записей и группировка по дням недели
                    data.appointments.forEach(appointment => {
                        let date = new Date(appointment.time);
                        let day = date.toLocaleDateString("ru-RU", { weekday: "long" });

                        if (!days[day]) {
                            days[day] = [];
                        }

                        days[day].push(`
                            <div class="record">
                                <span class="record-time">${date.toLocaleTimeString("ru-RU", { hour: '2-digit', minute: '2-digit' })}</span>
                                <span class="record-client">${appointment.client_name}</span>
                                <span class="record-service">${appointment.service_name}</span>
                                <span class="record-price">${appointment.price} ₽</span>
                            </div>
                        `);
                    });

                    // Выводим записи по дням недели
                    for (let day in days) {
                        recordGroup.innerHTML += `
                            <div class="record-day">
                                <h3>${day}</h3>
                                ${days[day].join("")}
                            </div>
                        `;
                    }
                } else {
                    recordGroup.innerHTML = `<h2>${data.message || 'Произошла ошибка'}</h2>`;
                }
            })
            .catch(error => console.error("Ошибка загрузки данных:", error));
    } else {
        alert("Ошибка: ID мастера не найден!");
    }
});



