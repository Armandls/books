describe("Sign up", () => {
    before(() => {
        cy.recreateDatabase();
    });
    it("[SU-1] shows the sign-up page", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up"]`).should("exist");
        cy.get(`[data-cy="sign-up__email"]`).should("exist");
        cy.get(`[data-cy="sign-up__password"]`).should("exist");
    });

    it("[SU-2] allows the user to sign-up correctly", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test001");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test001");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.location('pathname').should('eq', '/sign-in')
    });

    it("[SU-3] shows error when email does not have salle.url.edu", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@gmail.com");
        cy.get(`[data-cy="sign-up__password"]`).type("Test001");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test001");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongEmail"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongEmail"]`).invoke('text').should("eq", "Only emails from the domain @salle.url.edu are accepted.");
    });

    it("[SU-4] shows error when email is not a valid email", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student");
        cy.get(`[data-cy="sign-up__password"]`).type("Test001");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test001");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongEmail"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongEmail"]`).invoke('text').should("eq", "The email address is not valid.");
    });

    it("[SU-5] shows error when password has less than 7 characters", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongPassword"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongPassword"]`).invoke('text').should("eq", "The password must contain at least 7 characters.");
    });

    it("[SU-6] shows error when password does not follow correct format", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("TestTest");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("TestTest");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongPassword"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongPassword"]`).invoke('text').should("eq", "The password must contain both upper and lower case letters and numbers.");
    });

    it("[SU-7] shows error when passwords do not match", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("TestTest");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongPassword"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongPassword"]`).invoke('text').should("eq", "Passwords do not match.");
    });

    it("[SU-8] shows error when the number of coins is below the limit", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__coins"]`).type("-20");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongCoins"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongCoins"]`).invoke('text').should("eq", "Sorry, the number of Bitcoins is either below or above the limits.");
    });

    it("[SU-9] shows error when the number of coins is above the limit", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__coins"]`).type("50000");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongCoins"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongCoins"]`).invoke('text').should("eq", "Sorry, the number of Bitcoins is either below or above the limits.");
    });

    it("[SU-10] shows error when the number of coins is alphanumeric", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__coins"]`).type("asdfghjkl1234");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongCoins"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongCoins"]`).invoke('text').should("eq", "The number of Bitcoins is not a valid number.");
    });
    it("[SU-11] shows error when the number of coins is not a valid integer", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__coins"]`).type("69,69");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__wrongCoins"]`).should("exist");
        cy.get(`[data-cy="sign-up__wrongCoins"]`).invoke('text').should("eq", "The number of Bitcoins is not a valid number.");
    });

    it("[SU-12] shows email and number of coins when password is incorrect", () => {
        let email = "student@salle.url.edu";
        let password = "p";
        let coins = "100"

        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type(email);
        cy.get(`[data-cy="sign-up__password"]`).type(password);
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type(password);
        cy.get(`[data-cy="sign-up__coins"]`).type(coins);
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.get(`[data-cy="sign-up__email"]`).invoke('val').should("eq", email);
        cy.get(`[data-cy="sign-up__coins"]`).invoke('val').should("eq", coins);
    });

    it("[SU-13] allows user to sign-up without specifying number of coins", () => {
        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student2@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test1234");
        cy.get(`[data-cy="sign-up__btn"]`).click();
        cy.location('pathname').should('eq', '/sign-in')
    });
});
