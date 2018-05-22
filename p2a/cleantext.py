#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import re
import sys
import json
import string
import argparse


__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.

# https://stackoverflow.com/questions/367155/splitting-a-string-into-words-and-punctuation

def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings 
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """

    # YOUR CODE GOES BELOW:

    text = re.sub(r'[\n\t]', ' ', text)
    no_url = re.sub(r'https?:\/\/\S+', '', text)
    split = no_url.split()
    punctuated = []
    for token in split:
        token = token.lower()
        temp = re.findall(r"[\w'-]+|[.,!?;:]", token) #this is getting rid of commas in numbers: should be 100,000, not 100 000
        punctuated += temp

        '''
        TODO:
        fix cases with punctuation in the word/number: should be 100,000, not 100 000; should be i.e not i e
        fix cases with elipses: should be ..... not . . . . . . 
        fix cases with slashes inbetween characters: should be surrogate/regarding not surrogate regarding
        should be gt;on not gt ; on in some cases ???????
        don't get rid of % signs or $ signs

        '''


    parsed_text = ""
    not_punctuated = []
    
    if len(punctuated) > 0:
        parsed_text = punctuated[0]
        for i in range(1,len(punctuated)):
            temp = " " + punctuated[i]
            parsed_text += temp

        for token in punctuated:
            if token not in {'.', ',', '!', '?', ';', ':'}:
                not_punctuated.append(token)

    unigrams = ""
    bigrams = ""
    trigrams = ""

    if len(punctuated) > 0:
        if punctuated[0] not in {'.', ',', '!', '?', ';', ':'}:
            unigrams = punctuated[0]
        for i in range(1,len(punctuated)):
            if punctuated[i] in {'.', ',', '!', '?', ';', ':'}:
                continue
            if not unigrams:
                temp = punctuated[i]
            else:
                temp = " " + punctuated[i]
            unigrams += temp

    if len(punctuated) > 1:
        if punctuated[0] not in {'.', ',', '!', '?', ';', ':'} and punctuated[1] not in {'.', ',', '!', '?', ';', ':'}:
            bigrams = punctuated[0] + "_" + punctuated[1]
        for i in range(2,len(punctuated)):
            if punctuated[i] in {'.', ',', '!', '?', ';', ':'} or punctuated[i-1] in {'.', ',', '!', '?', ';', ':'}:
                continue
            temp = ""
            if not bigrams:
                temp = punctuated[i-1] + "_" + punctuated[i]
            else:
                temp =  " " + punctuated[i-1] + "_" + punctuated[i]
            bigrams += temp

    if len(punctuated) > 2:
        if punctuated[0] not in {'.', ',', '!', '?', ';', ':'} and punctuated[1] not in {'.', ',', '!', '?', ';', ':'} and punctuated[2] not in {'.', ',', '!', '?', ';', ':'}:
            trigrams = punctuated[0] + "_" + punctuated[1] + "_" + punctuated[2]
        for i in range(3,len(punctuated)):
            if punctuated[i] in {'.', ',', '!', '?', ';', ':'} or punctuated[i-1] in {'.', ',', '!', '?', ';', ':'} or punctuated[i-2] in {'.', ',', '!', '?', ';', ':'}:
                continue
            if not trigrams:
                temp = punctuated[i-2] + "_" + punctuated[i-1] + "_" + punctuated[i]
            else:
                temp = " " + punctuated[i-2] + "_" + punctuated[i-1] + "_" + punctuated[i]
            trigrams += temp

    return [parsed_text, unigrams, bigrams, trigrams]


if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.

    filename = sys.argv[1]
    file = open(filename, "r")
    for line in file:
        parsed_json = json.loads(line)
        print(sanitize(parsed_json["body"]))
        #sanitize(parsed_json["body"])

