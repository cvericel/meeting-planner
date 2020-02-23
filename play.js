const a = 10;

if (true) {
    const a = 42;
}


setTimeout(() => {
    console.log(a);
}, 100);

console.log("waiting...");