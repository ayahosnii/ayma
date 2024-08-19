import sys
import language_tool_python
import logging

def correct_grammar(sentence):
    try:
        # Initialize LanguageTool
        tool = language_tool_python.LanguageTool('en-US')

        # Check for grammar errors
        matches = tool.check(sentence)

        # Correct grammar errors
        corrected_sentence = language_tool_python.utils.correct(sentence, matches)

        return corrected_sentence
    except Exception as e:
        logging.error(f"Error occurred while correcting grammar: {str(e)}")
        return None

if __name__ == "__main__":
    # Configure logging
    logging.basicConfig(level=logging.INFO)

    if len(sys.argv) > 1:
        sentence = sys.argv[1]
        corrected_sentence = correct_grammar(sentence)
        if corrected_sentence:
            print(corrected_sentence)
        else:
            print("Failed to correct grammar. Please check the input.")
    else:
        print("No sentence provided.")
