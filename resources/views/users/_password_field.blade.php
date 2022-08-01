<label for="input-password">Password</label>
<input type="password"
       id="input-password"
       name="password"
       style="width:90%"
/>
<button type="button" onclick="toggleShowPassword()">
    Show Password
</button>
<button type="button" onclick="generatePassphrase()">
    Generate Random Passphrase
</button>

<script>
function toggleShowPassword() {
    var password_input = document.querySelector('#input-password');

    password_input.type = (password_input.type == 'text') ?
                          'password' :
                          'text' ;
}

/*
 * Kode di bawah adalah kode untuk generate passphrase di sisi client.
 * Diadaptasi dari kode open source di cosmopass.xyz
 */

// berisi array dari kata2 (wordlist) yang akan kita gunakan utk generate passphrase
var wordlist = null;
var WORD_NUM = 4;
const wordlist_url = '{{ asset('wordlist_id.txt') }}';

function generatePassphrase() {
    // actual passphrase generation
    random_array = new Uint16Array(WORD_NUM);
    window.crypto.getRandomValues(random_array);

    var result = Array.from(random_array).map(function(val, idx) {

        return wordlist[val % wordlist.length];
    });

    document.querySelector('#input-password').value = result.join(' ');
}

function loadWordlist() {
    console.log('start loading wordlist...');
    // load wordlist
    return fetch(wordlist_url)
           .then(response => response.text())
           .then(response => {
               wordlist = response.split("\n").map(s => {
                 // clean diceware wordlist numbering
                 // ref: https://javascript.info/regexp-methods#str-replace-str-regexp-str-func
                 return s.replace(/^\d+\s/,'').trim();
               });

               console.log('wordlist loaded');
           });
}

loadWordlist();
</script>

