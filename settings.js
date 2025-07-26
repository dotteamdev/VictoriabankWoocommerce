function setKeyName(keyFileName){
    const pathFields = keyFileName.split('/')

    return pathFields[pathFields.length - 1];
}

function createFile(keyName, fieldName){
    return new File([fieldName], keyName, {
        type: 'text/plain',
        lastModified: new Date(),
    });
}

function setFileToField(fieldId, fieldName, keyFileName){
    const keyInput = document.getElementById(fieldId);
    const keyFile = createFile(setKeyName(keyFileName), fieldName);
    const keyDataTransfer = new DataTransfer();

    keyDataTransfer.items.add(keyFile);
    keyInput.files = keyDataTransfer.files;
}