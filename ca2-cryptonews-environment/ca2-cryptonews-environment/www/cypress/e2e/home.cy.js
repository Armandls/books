describe("Routes", () => {
    before(() => {
        cy.recreateDatabase();

        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test001");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test001");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
    });

    it("[R-1] shows the homepage to an unauthorized user", () => {
        cy.visit("/");
        cy.get(`[data-cy="home__welcomeMsg"]`).should("exist");
        cy.get(`[data-cy="home__welcomeMsg"]`)
            .invoke("text")
            .should("eq", "Hello stranger!");
    });

    it("[R-2] shows correct message to an authorized user", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.location("pathname").should("eq", "/");
        cy.get(`[data-cy="home__welcomeMsg"]`).should("exist");
        cy.get(`[data-cy="home__welcomeMsg"]`)
            .invoke("text")
            .should("eq", "Hello student3!");
    });
});
