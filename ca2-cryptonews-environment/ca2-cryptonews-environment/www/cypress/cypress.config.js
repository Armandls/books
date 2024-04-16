const { defineConfig } = require('cypress')
const fs = require('fs')

module.exports = defineConfig({
    e2e: {
        specPattern: "e2e/**/*.cy.{js,jsx,ts,tsx}",
        supportFile: "support/index.js",
        screenshotsFolder: "screenshots/",
        video: false,
        setupNodeEvents(on, config) {
            // `on` is used to hook into various events Cypress emits
            // `config` is the resolved Cypress config
            // Usage: cy.task('queryDb', query)
            on("task", {
                env: () => process.env,
                log: message => { console.log(message); return null },
                mkdir: path => { fs.mkdirSync(path, { recursive: true }); return null },
                rm: path => { fs.rmSync(path, { recursive: true }); return null }
            })
        }
    }
})
