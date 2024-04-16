describe("Market", () => {
    before(() => {
        cy.recreateDatabase();

        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test001");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test001");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
    });

    it("[R-1] shows correct message to an unauthorized user", () => {
        cy.visit("/mkt");
        cy.get(`[data-cy="market-updates__title"]`).should("exist");
        cy.get(`[data-cy="market-updates__title"]`)
            .invoke("text")
            .should("eq", "Welcome to CryptoNews! Login if you want to see your updated data.");
    });

    it("[R-2] shows correct message to an authorized user", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.visit("/mkt");
        cy.get(`[data-cy="market-updates__title"]`).should("exist");
        cy.get(`[data-cy="market-updates__title"]`)
            .invoke("text")
            .should("eq", "Market Updates");
    });

    it("[R-3] shows user crypto balance", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.visit("/mkt");
        cy.get(`[data-cy="market-updates__user-cryptobalance"]`).should("exist");
    });

    it("[R-4] shows info of at least one cryptocurrency", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.visit("/mkt");
        cy.get(`[data-cy="market-updates__user-cryptobalance"]`).should("exist");
        cy.get(`[data-cy="market-updates__item"]`).should("exist");
        cy.get(`[data-cy="market-updates__item-title"]`).should("exist");
        cy.get(`[data-cy="market-updates__item-price"]`).should("exist");
    });
})