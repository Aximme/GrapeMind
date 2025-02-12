import torch
import torch.nn as nn

class TextClassifier(nn.Module):
    def __init__(self, vocab_size, embedding_dim, hidden_dim, output_dim, pad_idx):
        super(TextClassifier, self).__init__()
        self.embedding = nn.Embedding(vocab_size, embedding_dim, padding_idx=pad_idx)
        self.lstm = nn.LSTM(embedding_dim, hidden_dim, batch_first=True)
        self.fc = nn.Linear(hidden_dim, output_dim)
    
    def forward(self, text):
        # text: [batch_size, seq_len]
        embedded = self.embedding(text)  # [batch_size, seq_len, embedding_dim]
        _, (hidden, _) = self.lstm(embedded)
        hidden = hidden.squeeze(0)  # [batch_size, hidden_dim]
        logits = self.fc(hidden)    # [batch_size, output_dim]
        return logits

def train_model(model, dataloader, optimizer, criterion, device):
    model.train()
    total_loss = 0.0
    for texts, labels in dataloader:
        lengths = [len(x) for x in texts]
        max_len = max(lengths)
        padded_texts = [x + [0]*(max_len - len(x)) for x in texts]
        texts_tensor = torch.tensor(padded_texts, dtype=torch.long, device=device)
        labels = labels.to(device)
        
        optimizer.zero_grad()
        outputs = model(texts_tensor)
        loss = criterion(outputs, labels)
        loss.backward()
        optimizer.step()
        total_loss += loss.item()
    return total_loss / len(dataloader)

def predict(model, text, text_vocab, device):
    from data_loader import encode_text
    encoded = encode_text(text, text_vocab)
    input_tensor = torch.tensor([encoded], dtype=torch.long, device=device)
    with torch.no_grad():
        output = model(input_tensor)
        probs = torch.sigmoid(output).squeeze(0)
    return probs.cpu().numpy()
