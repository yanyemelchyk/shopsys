#!/usr/bin/env node

const ncp = require('ncp').ncp;
const fs = require('fs');

const assets = [
    {
        source: 'web/bundles/fpjsformvalidator',
        destination: 'assets/js/bundles/fpjsformvalidator'
    },
    {
        source: 'node_modules/@shopsys/framework/public/admin',
        destination: 'web/public/admin'
    },
    {
        source: 'assets/public',
        destination: 'web/public'
    }
];

assets.forEach(item => {
    fs.mkdirSync(item.destination, { recursive: true });

    ncp(item.source, item.destination, err => {
        if (err) {
            return console.error(err);
        }
        console.log(`Source folder ${item.source} was copied.`);
    });
});
