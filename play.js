class AGreatClass {
    constructor(greatNumber) {
        this.greatNumber = greatNumber
    }

    returnGreatThings() {
        return this.greatNumber
    }
}

class AnotherGreatClass extends AGreatClass {
    constructor(greatNumber, greatWord) {
        super(greatNumber);
        this.greatWord = greatWord;
    }

    returnGreatThings() {
        return [this.greatNumber, this.greatWord];
    }
}

const aGreatObject = new AnotherGreatClass(32, "bite");
console.log(
    aGreatObject.returnGreatThings()
);