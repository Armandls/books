describe("News", () => {
    before(() => {
        cy.recreateDatabase();

        cy.visit("/sign-up");
        cy.get(`[data-cy="sign-up__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-up__password"]`).type("Test001");
        cy.get(`[data-cy="sign-up__repeatPassword"]`).type("Test001");
        cy.get(`[data-cy="sign-up__coins"]`).type("100");
        cy.get(`[data-cy="sign-up__btn"]`).click();
    });

    it("[R-1] shows the sign-in page when unauthorized user tries to access news page", () => {
        cy.visit("/news");
        cy.location("pathname").should("eq", "/sign-in");
        cy.get(`[data-cy="sign-in"]`).should("exist");
        cy.get(`[data-cy="sign-in__message"]`).should("exist");
        cy.get(`[data-cy="sign-in__message"]`)
            .invoke("text")
            .should("eq", "You must be logged in to access the news page.");
    });

    it("[R-2] shows the news page to a user", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.visit("/news");
        cy.get(`[data-cy="news-articles__title"]`).should("exist");
    });

    it("[R-3] shows a list of news", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.visit("/news");
        cy.get(`[data-cy="news-articles__list"]`).should("exist");
    });

    it("[R-4] news have a correct structure", () => {
        cy.visit("/sign-in");
        cy.get(`[data-cy="sign-in__email"]`).type("student3@salle.url.edu");
        cy.get(`[data-cy="sign-in__password"]`).type("Test001");
        cy.get(`[data-cy="sign-in__btn"]`).click();
        cy.visit("/news");
        cy.get(`[data-cy="news-articles__item"]`).should("exist");
        cy.get(`[data-cy="news-articles__item-title"]`).should("exist");
        cy.get(`[data-cy="news-articles__item-date"]`).should("exist");
        cy.get(`[data-cy="news-articles__item-author"]`).should("exist");
        cy.get(`[data-cy="news-articles__item-summary"]`).should("exist");
    });
})