function updateDateRequirements(dates, textareas) {
    textareas.forEach((area) => {
        if (!area.value) {
            dates.forEach((date) => {
                date.required = true;
                return true;
            });
        } else {
            dates.forEach((date) => {
                date.required = false;
                return false;
            });
        }
    });
}

const submit = document.querySelector('input[type="submit"]');
const dates = document.querySelectorAll('input[type="date"]');
const textareas = document.querySelectorAll('textarea');

submit.addEventListener('click', function (e) {
    e.preventDefault();
    action = updateDateRequirements(dates, textareas);
    if (action) {
        document.querySelector('form').submit();
    }
});
