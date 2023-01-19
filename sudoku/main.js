let ex = [], ey = [], arr, count = 0;

let getEmpty = () => { //ex는 가로, ey는 세로
    for (let y = 0; y < 9; y++) {
        for (let x = 0; x < 9; x++) {
            if (arr[y][x].length !== 1) {
                ey.push(y);
                ex.push(x);
            }
        }
    }
}

let checkBox = (coord) => {
    let coords = [[0, 1, 2], [3, 4, 5], [6, 7, 8]];
    for(let set of coords) 
        for(let num of set)
            if (num === coord) return set;
}

let fill = () => { //arr로 채움
    let inputs = document.querySelectorAll("input");
    let count = 0;

    for (let y = 0; y < arr.length; y++) {
        for (let x = 0; x < arr[y].length; x++) {
            if (arr[y][x].length === 1) {
                inputs[count].value = arr[y][x][0];
            }
            count++;
        }
    }
}

let createArray = () => {
    let inputs = document.querySelectorAll("input");
    arr = new Array(9);
    let count = 0;

    for (let i = 0; i < arr.length; i++) {
        arr[i] = new Array(9);
        for (let j = 0; j < arr[i].length; j++) {
            let input;
            if (inputs[count].value !== "") {
                input = [parseInt(inputs[count].value)];
            } else {
                input = [];
            }
            arr[i][j] = input;
            count++;
        }
    }
}

let isValid = (y, x, n) => {
    for (let i = 0; i < 9; i++) { //세로
        if (arr[i][x].length === 1 && arr[i][x][0] === n) {
            return false;
        }
    }

    for (let j = 0; j < 9; j++) { //가로
        if (arr[y][j].length === 1 && arr[y][j][0] === n) {
            return false;
        }
    }

    let squareInputs = [checkBox(y), checkBox(x)]; //3*3박스
    for(let i of squareInputs[0]) {
        for(let j of squareInputs[1]) {
            if (arr[i][j].length === 1 && arr[i][j][0] === n) {
                return false;
            }
        }
    }
    return true;
}

let isValid_ = (array) => {
    for (var y = 0; y < 9; ++y) {
        for (var x = 0; x < 9; ++x) {
            var value = array[y][x][0];
            if (value) {
                if (isNaN(value) || value < 1)
                    return false;
                //가로
                for (var i = 0; i < 9; ++i) 
                    if (i != x && array[y][i][0] == value) 
                        return false;
                //세로
                for (var j = 0; j < 9; ++j) 
                    if (j != y && array[j][x][0] == value) 
                        return false;
                //3*3박스
                var y_ = Math.floor(y / 3) * 3;
                for (var j = y_; j < y_ + 3; ++j) {
                    var x_ = Math.floor(x / 3) * 3;
                    for (i = x_; i < x_ + 3; ++i) {
                        if ((i != x || j != y) && array[j][i][0] == value) {
                            return false;
                        }
                    }
                }
            }
        }
    }
    return true;
}

let recurse = (index) => { //풀이
    count++;
    if (index === ex.length) return true;
    for (let n = 1; n <= 9; n++) {
        if (isValid(ey[index], ex[index], n)) {
            arr[ey[index]][ex[index]][0] = n;
            let isDone = recurse(index + 1);
            if (isDone) return true;
        }
    }
    arr[ey[index]][ex[index]] = [];
}

let solveSudoku = () => {
    createArray();
    getEmpty();

    if (isValid_(arr)) {
        recurse(0);
        fill();
    }
    else {    
        ex = []; ey = []; count = 0;
        alert('Not Valid.');
    }
}

let clearSudoku = () => {
    ex = []; ey = []; count = 0;
    arr = new Array(9);
    let inputs = document.querySelectorAll("input");

    for (let y = 0; y < 9; y++) {
        for (let x = 0; x < 9; x++) {
            inputs[count].value = [];
            count++;
        }
    }
}

let body = document.querySelector("body");
let container = document.getElementById("index");
let block = document.createElement("div");
block.className = "ui grid";
body.appendChild(block);

let block_1 = document.createElement("div");
let block_2 = document.createElement("div");
let block_3 = document.createElement("div");
block_1.className = "one wide column";
block_2.className = "fourteen wide column";
block_3.className = "one wide column";
block.appendChild(block_1);
block.appendChild(block_2);
block.appendChild(block_3);

let intro = document.createElement("h1");
intro.innerText = "Sudoku Solver";
intro.className = "ui header";
block_2.appendChild(intro);

container.className = "contain";
block_2.appendChild(container);

for (let y = 0; y < 9; y++) {
    let row = document.createElement("div");
    for (let x = 0; x < 9; x++) {
        let cell = document.createElement("input");
        cell.type = "text";
        cell.setAttribute("maxLength", 1);
        row.appendChild(cell);
    }
    container.appendChild(row);
}

block_2.appendChild(document.createElement("br"));

let solver = document.createElement("button");
solver.innerText = "Solve";
solver.className = "btn";
solver.onclick = solveSudoku;
block_2.appendChild(solver);

let clear = document.createElement("button");
clear.innerText = "Clear";
clear.className = "btn2";
clear.onclick = clearSudoku;
block_2.appendChild(clear);