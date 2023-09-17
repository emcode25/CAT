from TTS.api import TTS
from datetime import datetime
from playsound import playsound


api = TTS(model_name="tts_models/eng/fairseq/vits").to("cpu")
def toSpeach(msg):
	filepath = datetime.utcnow().strftime('%H%M%S')+"output.wav"
	api.tts_to_file(msg, file_path=filepath)
	playsound(filepath)
	