const DATA = [
    {
        question: '<div style="position: relative; text-align: center;"><img src="https://i.pinimg.com/474x/87/87/b3/8787b391835f5bb662b0f41318faeb33.jpg"></div> <br><b>Sophie is a character from which anime movie?</b>',
        answers: [
            {
                id: '1',
                value: 'GO! Princess PreCure',
                correct: true,
            },
            {
                id: '2',
                value: 'Show white with the red hair',
                correct: false,
            },
            {
                id: '3',
                value: 'Howls moving castle',
                correct: false,
            },
        ],

    },
    {
        question: 'In Dragon Ball Super, Goku is sent to destroy earth. What happened to make him change his ways?',
        answers: [
            {
                id: '4',
                value: 'He just realised it was quiet mean',
                correct: false,
            },
            {
                id: '5',
                value: 'He bumped his head',
                correct: false,
            },
            {
                id: '6',
                value: 'He couldnt be bothered in the end',
                correct: true,
            },
        ]
    },
    {
        question: 'What do you have to do with Pokemon?',
        answers: [
            {
                id: '7',
                value: 'Snatch em all!',
                correct: false,
            },
            {
                id: '8',
                value: 'Fetch em all!',
                correct: false,
            },
            {
                id: '9',
                value: 'Catch em all!',
                correct: true,
            },
        ]
    },
    {
        question: 'How do you do a Naruto run?',
        answers: [
            {
                id: '10',
                value: 'Put your head forward and arms back',
                correct: false,
            },
            {
                id: '11',
                value: 'Put your left foot in, your left foot out',
                correct: false,
            },
            {
                id: '12',
                value: 'You do the hokey cokey and you turn around',
                correct: true,
            },
        ]
    },
    {
        question: 'Who is this chubby baby from Spirited Away?',
        answers: [
            {
                id: '13',
                value: 'Buddy',
                correct: false,
            },
            {
                id: '14',
                value: 'Boh',
                correct: false,
            },
            {
                id: '15',
                value: 'Brian',
                correct: true,
            },
        ]
    },
    {
        question: 'How do you spell anime in Japanese? Hint – only one of these answers is in Japanese!',
        answers: [
            {
                id: '16',
                value: 'アニメ',
                correct: true,
            },
            {
                id: '17',
                value: 'أنيمي',
                correct: false,
            },
            {
                id: '18',
                value: 'אַנימע',
                correct: false,
            },
        ]
    },
    {
        question: 'Fill in the blank: Dragon ___ Z',
        answers: [
            {
                id: '19',
                value: 'Beach',
                correct: false,
            },
            {
                id: '20',
                value: 'Ball',
                correct: false,
            },
            {
                id: '21',
                value: 'Mech',
                correct: true,
            },
        ]
    },
    {
        question: 'What kind of person is Naruto?',
        answers: [
            {
                id: '22',
                value: 'A samurai',
                correct: false,
            },
            {
                id: '23',
                value: 'A tree surgeon',
                correct: false,
            },
            {
                id: '24',
                value: 'A ninja',
                correct: true,
            },
        ]
    },
    {
        question: 'Anime HAS to be made in Japan, or you cant call it anime. True or false?',
        answers: [
            {
                id: '25',
                value: 'Truuuue!',
                correct: false,
            },
            {
                id: '26',
                value: 'Faaaaalse!',
                correct: false,
            },
            {
                id: '27',
                value: 'I dont watch anime',
                correct: true,
            },
        ]
    },
    {
        question: '<img src="images/54207_blob.png" width="500px" height="300px"> <br> Which famous anime film is this from?',
        answers: [
            {
                id: '28',
                value: 'Spirited Away',
                correct: false,
            },
            {
                id: '29',
                value: 'Howl&#039;s Moving Castle',
                correct: false,
            },
            {
                id: '30',
                value: 'My Neighbour Totoro',
                correct: true,
            },
        ]
    },
];


let localResults = {};



const quiz = document.getElementById('quiz');
const questions = document.getElementById('questions');
const indicator = document.getElementById('indicator');
const results = document.getElementById('results');
const btn_next = document.getElementById('btn-next');
const btn_restart = document.getElementById('btn-restart');



const renderQuestions = (index) => {
    renderIndicators(index + 1);

    questions.dataset.currentStep = index;

    const renderAnswers = () => DATA[index].answers
    .map((answer) => `
            <li>
                <label>
                    <input class="answer-input" type="radio" name=${index} value=${answer.id}>
                        ${answer.value}
                </label>
            </li>
        `)
        .join('');

    questions.innerHTML = `
    <div class="quiz-questions-item">
                <div class="quiz-questions-item-question">${DATA[index].question}</div>
                <ul class="quiz-questions-item-answers">${renderAnswers()}</ul>
            </div>
    `;
};

const renderResults = () => {
    let content = '';

    const getClassname = (answer, questionIndex) => {
        let classname = '';

        if(!answer.correct && answer.id === localResults[questionIndex]){
            classname = 'answer-invalid';
        } else if(answer.correct){
            classname = 'answer-valid';
        }

        return classname;
    };

    const getAnswers = (questionIndex) => DATA[questionIndex].answers
        .map((answer) => `<li class=${getClassname(answer, questionIndex)}>${answer.value}</li>`)
        .join('');

    DATA.forEach((question, index) => {
        content += `
            <div class="quiz-results-item">
                <div class="quiz-results-item-question">${question.question}</div>
                <ul class="quiz-results-item-answers">${getAnswers(index)}</ul>
            </div>
        `;
    });


    results.innerHTML = content;
};

const renderIndicators = (currentStep) => {
    indicator.innerHTML = `${currentStep}/${DATA.length}`
};

quiz.addEventListener('change', (event) => {
    //Answer logic

    if (event.target.classList.contains('answer-input')) {
        localResults[event.target.name] = event.target.value;
        btn_next.disabled = false;
    }
});

quiz.addEventListener('click', (event) => {
    //Next or restart
    if (event.target.classList.contains('btn-next')) {
        console.log('Next');
        const nextQuestionIndex = Number(questions.dataset.currentStep) + 1;


        if(DATA.length === nextQuestionIndex){
            //goes to results
            questions.classList.add('question-hidden');
            indicator.classList.add('indicator-hidden');
            results.classList.add('indicator-visible');
            btn_next.classList.add('btn-next-hidden');
            btn_restart.classList.add('btn-restart-visible');
            renderResults();
        } else{
            //goes to next question
            renderQuestions(nextQuestionIndex);
        }


        btn_next.disabled = true;
    }

    if (event.target.classList.contains('btn-restart')) {
        console.log('Restart');

        localResults = {};
        results.innerHTML = '';

        questions.classList.remove('question-hidden');
        indicator.classList.remove('indicator-hidden');
        results.classList.remove('indicator-visible');
        btn_next.classList.remove('btn-next-hidden');
        btn_restart.classList.remove('btn-restart-visible');

        renderQuestions(0);
    }
});

renderQuestions(0);
