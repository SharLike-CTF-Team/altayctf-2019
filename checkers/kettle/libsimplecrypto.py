import time
import base64
import binascii
from secrets import token_hex
from rsa import prime
from arc4 import ARC4


def gcd(a, b):
    while b != 0:
        a, b = b, a % b
    return a

def lcm(a, b):
    return a * b // gcd(a, b)

def imod(a, n):
    i = 1
    while True:
        c = n * i + 1
        if(c % a == 0):
            c = c//a
            break
        i = i+1
    return c

def arc4_encrypt(key, message):
    arc4 = ARC4(key)
    cipher = arc4.encrypt(message)
    return cipher

def arc4_decrypt(key, cipher):
    arc4 = ARC4(key)
    plain = arc4.decrypt(cipher)
    return plain

class SimpleRSA:
    def __init__(self, bit_length=256):
        p, q = 0, 0
        while p == q:
            p = prime.getprime(bit_length)
            q = prime.getprime(bit_length)
        self.p = p
        self.q = q

        self.N = self.p*self.q
        self.phi = lcm(self.p - 1, self.q - 1)
        self.e = 65537
        self.d = imod(self.e, self.phi)

    def dump(self):
        return (self.p, self.q, self.N, self.phi, self.e, self.d)

    def load(self, p, q, N, phi, e, d):
        self.p = p
        self.q = q
        self.N = N
        self.phi = phi
        self.e = e
        self.d = d

    def get_pub(self):
        return (self.N, self.e)

    def get_priv(self):
        return (self.N, self.d)
    
    def encrypt(self, m, other_pubkey):
        if not isinstance(m, int):
            m = int(binascii.hexlify(m.encode()))
        return pow(m, other_pubkey[1], other_pubkey[0])

    def decrypt(self, c):
        res = pow(c, self.d, self.N)
        return binascii.unhexlify(str(res))


class Cipher(SimpleRSA):
    def __init__(self, params):
        self.p = params['p']
        self.q = params['q']
        self.N = params['N']
        self.phi = params['phi']
        self.e = params['e']
        self.d = params['d']
    
    def decrypt_request(self, tmpkey, message):
        k = self.decrypt(tmpkey)
        message = arc4_decrypt(k, message)
        return message
    
    def encrypt_response(self, user_key, message):
        tmpkey = token_hex(nbytes=10)
        other_key = (user_key['user_key']['N'], user_key['user_key']['e'])
        enc_key = self.encrypt(tmpkey, other_key)
        cipher = arc4_encrypt(tmpkey, message)
        return dict(
            key=enc_key,
            data=str(base64.b64encode(cipher))[2:-1]
        )

if __name__ == "__main__":
    pass