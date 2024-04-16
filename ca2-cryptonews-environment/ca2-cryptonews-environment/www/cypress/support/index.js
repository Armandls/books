// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

const cypressDirPath = `.`;
const cypressTmpDirPath = `${cypressDirPath}/tmp`
const dumpFile = `${cypressTmpDirPath}/dump.sql`;
const schemaFile = `${cypressTmpDirPath}/schema.sql`;

// Database connection settings.
const db = {
    host: 'mysql',
    user: 'root',
    password: '',
    database: ''
}

function recreateTestDatabase() {
    // Dump lscoins database
    cy.exec(`mysqldump -u${db.user} -p${db.password} -h${db.host} --no-data ${db.database} > ${schemaFile}`)
        .then(() => cy.exec(`mysql -u${db.user} -p${db.password} -h${db.host} ${db.database} < ${schemaFile}`));
}


Cypress.Commands.add('recreateDatabase', recreateTestDatabase);


before(() => {
    cy.task('mkdir', cypressTmpDirPath)
        .then(() => {
            cy.task('env').then(env => {
                db.password = env.DB_ROOT_PASSWORD
                db.database = env.DB_DATABASE

                cy.exec(`mysqldump -u${db.user} -p${db.password} -h${db.host} ${db.database} > ${dumpFile}`);
            })
        })
    // Read the environment variables

});

after(() => {
    cy.exec(`mysql -u${db.user} -p${db.password} -h${db.host} ${db.database} < ${dumpFile}`);
    db.password = ''
    db.database = ''
    cy.task('rm', cypressTmpDirPath)
});